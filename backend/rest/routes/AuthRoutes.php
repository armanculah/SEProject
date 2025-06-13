<?php

require_once __DIR__ . '/../dao/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


Flight::group('/auth', function() {
   /**
    * @OA\Post(
    *     path="/auth/register",
    *     summary="Register new user.",
    *     description="Add a new user to the database.",
    *     tags={"auth"},
    *     @OA\RequestBody(
    *         description="Add new user",
    *         required=true,
    *         @OA\MediaType(
    *             mediaType="application/json",
    *             @OA\Schema(
    *                 required={"user_name", "email", "password"},
    *                 @OA\Property(property="user_name", type="string", example="demo_user", description="User name"),
    *                 @OA\Property(property="email", type="string", example="demo@gmail.com", description="User email"),
    *                 @OA\Property(property="password", type="string", example="some_password", description="User password")
    *             )
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="User has been added."
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Missing or invalid fields."
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Internal server error."
    *     )
    * )
    */
   Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();

        if (!isset($data['user_name']) || !isset($data['email']) || !isset($data['password'])) {
            Flight::halt(400, 'Username, email, and password are required.');
        }

        if (trim($data['user_name']) == "" || trim($data['email']) == "" || trim($data['password']) == "") {
            Flight::halt(400, 'Username, email, and password cannot be empty.');
        }

        $service = Flight::auth_service();
        error_log('DEBUG: auth_service type: ' . gettype($service) . ' class: ' . (is_object($service) ? get_class($service) : 'not an object'));
        if ($service === null) {
            error_log('DEBUG: auth_service is null in /auth/register');
            Flight::halt(500, 'Internal error: auth_service not available');
        }
        $result = $service->register($data);
        if (!$result['success']) {
            Flight::halt(400, $result['error']);
        }
        $user = $result['data'];

        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60) // valid for an hour
        ];

        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        Flight::json(
            array_merge($user, ['token' => $token])
        );

});
   /**
    * @OA\Post(
    *      path="/auth/login",
    *      tags={"auth"},
    *      summary="Login to system using email and password",
    *      @OA\Response(
    *           response=200,
    *           description="Student data and JWT"
    *      ),
    *      @OA\RequestBody(
    *          description="Credentials",
    *          @OA\JsonContent(
    *              required={"email","password"},
    *              @OA\Property(property="email", type="string", example="demo@gmail.com", description="Student email address"),
    *              @OA\Property(property="password", type="string", example="some_password", description="Student password")
    *          )
    *      )
    * )
    */
   Flight::route('POST /login', function() {
        $payload = Flight::request()->data->getData();

        $user = Flight::auth_service()->get_user_by_email($payload['email']);

        if (!$user || !password_verify($payload['password'], $user['password']))
            Flight::halt(500, "Invalid username or password");

        unset($user['password']);

        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60) // valid for an hour
        ];

        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        Flight::json(
            array_merge($user, ['token' => $token])
        );
    });
});
?>
