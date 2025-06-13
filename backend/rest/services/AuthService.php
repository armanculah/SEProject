<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthService extends BaseService {
   private $auth_dao;
   public function __construct() {
       error_log('DEBUG: Constructing AuthService');
       $dao = new AuthDao();
       $this->auth_dao = $dao;
       parent::__construct($dao);
   }


   public function get_user_by_email($email){
       return $this->auth_dao->get_user_by_email($email);
   }


   public function register($entity) {  
       if (empty($entity['user_name']) || empty($entity['email']) || empty($entity['password'])) {
           return ['success' => false, 'error' => 'Username, email, and password are required.'];
       }


       $email_exists = $this->auth_dao->get_user_by_email($entity['email']);
       if ($email_exists) {
           return ['success' => false, 'error' => 'Email already registered.'];
       }

       $entity['password'] = password_hash($entity['password'], PASSWORD_BCRYPT);

       if (!isset($entity['role_id'])) {
           $entity['role_id'] = 2; // Default role for new users
       }

       $created = parent::create($entity);
       if (!$created) {
           return ['success' => false, 'error' => 'Failed to create user.'];
       }

       unset($entity['password']);

       return ['success' => true, 'data' => $entity];
   }

   public function login($entity) {
       if (empty($entity['email']) || empty($entity['password'])) {
           return ['success' => false, 'error' => 'Email and password are required.'];
       }

       $user = $this->auth_dao->get_user_by_email($entity['email']);
       if (!$user || !password_verify($entity['password'], $user['password'])) {
           return ['success' => false, 'error' => 'Invalid username or password.'];
       }

       unset($user['password']);

       $jwt_payload = [
           'user' => $user,
           'iat' => time(),
           'exp' => time() + (60 * 60 * 24) // valid for a day
       ];

       $token = JWT::encode(
           $jwt_payload,
           Config::JWT_SECRET(),
           'HS256'
       );

       return ['success' => true, 'data' => array_merge($user, ['token' => $token])];
   }
}