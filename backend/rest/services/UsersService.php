<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/UsersDao.php'; // Corrected path to UsersDao.php

class UsersService extends BaseService {
    private $usersDao;

    public function __construct() {
        $this->usersDao = new UsersDao();
    }

    public function getAllUsers() {
        return $this->usersDao->getAll();
    }

    public function getAllUsersWithRoles() {
        return $this->usersDao->getAllUsersWithRoles();
    }

    public function getUserById($userId) {
        return $this->usersDao->getUserById($userId);
    }

    public function createUser($data) {
        return $this->usersDao->insert($data);
    }

    public function updateUser($userId, $data) {
        return $this->usersDao->updateUser($userId, $data);
    }

    public function deleteUser($userId) {
        return $this->usersDao->delete($userId);
    }

    public function getUserByEmail($email) {
        return $this->usersDao->getUserByEmail($email);
    }

    public function updateCredentials($userId, $email, $password) {
        return $this->usersDao->updateCredentials($userId, $email, $password);
    }

    public function updateImage($userId, $imageUrl) {
        return $this->usersDao->updateImage($userId, $imageUrl);
    }

    public function login($data) {
        if (empty($data['email']) || empty($data['password'])) {
            return ['error' => 'Email and password are required'];
        }

        $user = $this->usersDao->getUserByEmail($data['email']);

        if ($user && password_verify($data['password'], $user['password'])) {
            return ['message' => 'Login successful', 'user' => $user];
        }

        return ['error' => 'Invalid email or password'];
    }

    public function signup($data) {
        if (empty($data['user_name']) || empty($data['email']) || empty($data['password'])) {
            return ['error' => 'User name, email, and password are required'];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $userData = [
            'email' => $data['email'],
            'password' => $data['password'],
            'user_name' => $data['user_name'],
            'role_id' => $data['role_id'] ?? 2,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->usersDao->insert($userData);
        return ['message' => 'User registered successfully'];
    }

    public function getProfile($userId) {
        return $this->usersDao->getById($userId);
    }

    public function getAdminProfile($userId) {
        $user = $this->usersDao->getUserByIdWithRole($userId);

        if (empty($user) || $user['role_id'] !== 1) {
            throw new Exception('Unauthorized access: User is not an admin');
        }

        return $user;
    }
}
?>
