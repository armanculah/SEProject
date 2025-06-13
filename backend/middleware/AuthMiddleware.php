<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('require_auth')) {
function require_auth() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        error_log("[" . date('Y-m-d H:i:s') . "] Unauthorized access attempt: Missing Authorization header");
        Flight::halt(401, json_encode(['status' => 'error', 'message' => 'Unauthorized - Missing Authorization header']));
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    try {
        $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
        Flight::set('user', $decoded->user);
    } catch (Exception $e) {
        error_log("[" . date('Y-m-d H:i:s') . "] Unauthorized access attempt: Invalid token - " . $e->getMessage());
        Flight::halt(401, json_encode(['status' => 'error', 'message' => 'Unauthorized - Invalid token']));
    }
}
}

if (!function_exists('require_role')) {
function require_role($role) {
    require_auth();
    $user = Flight::get('user');

    if (!isset($user->role) || $user->role !== $role) {
        error_log("[" . date('Y-m-d H:i:s') . "] Forbidden access attempt: Insufficient role - Required: $role, Provided: " . ($user->role ?? 'none'));
        Flight::halt(403, json_encode(['status' => 'error', 'message' => 'Forbidden - Insufficient role', 'required_role' => $role]));
    }
}
}

if (!class_exists('AuthMiddleware')) {
class AuthMiddleware {
   public function verifyToken($token){
       if(!$token) {
           error_log("[" . date('Y-m-d H:i:s') . "] Missing authentication header");
           Flight::halt(401, json_encode(['status' => 'error', 'message' => 'Missing authentication header']));
       }
       try {
           $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
           Flight::set('user', $decoded_token->user);
           Flight::set('jwt_token', $token);
           return TRUE;
       } catch (Exception $e) {
           error_log("[" . date('Y-m-d H:i:s') . "] Invalid token: " . $e->getMessage());
           Flight::halt(401, json_encode(['status' => 'error', 'message' => 'Invalid token']));
       }
   }
   public function authorizeRole($requiredRole) {
       $user = Flight::get('user');
       if ($user->role !== $requiredRole) {
           error_log("[" . date('Y-m-d H:i:s') . "] Access denied: insufficient privileges for role $requiredRole");
           Flight::halt(403, json_encode(['status' => 'error', 'message' => 'Access denied: insufficient privileges']));
       }
   }
   public function authorizeRoles($roles) {
       $user = Flight::get('user');
       if (!in_array($user->role, $roles)) {
           error_log("[" . date('Y-m-d H:i:s') . "] Forbidden: role not allowed");
           Flight::halt(403, json_encode(['status' => 'error', 'message' => 'Forbidden: role not allowed']));
       }
   }
   function authorizePermission($permission) {
       $user = Flight::get('user');
       if (!in_array($permission, $user->permissions)) {
           error_log("[" . date('Y-m-d H:i:s') . "] Access denied: missing permission $permission");
           Flight::halt(403, json_encode(['status' => 'error', 'message' => 'Access denied: permission missing']));
       }
   }   
}
}
