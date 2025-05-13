<?php
require_once __DIR__ . '/BaseDao.php';

class UsersDao extends BaseDao {

    public function __construct() {
        parent::__construct("users", "user_id");
    }

    public function getUserByEmail($email) {
        error_log('Executing query: SELECT * FROM users WHERE email = :email');
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        error_log('Querying user by email: ' . $email);
        $result = $stmt->fetch();
        error_log('Query result: ' . json_encode($result));
        return $result;
    }

    public function getAllWithRoles() {
        $stmt = $this->connection->prepare("
            SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.role_id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateCredentials($user_id, $email, $password) {
        $stmt = $this->connection->prepare("
            UPDATE users SET email = :email, password = :password WHERE user_id = :user_id
        ");
        return $stmt->execute([
            'email' => $email,
            'password' => $password,
            'user_id' => $user_id
        ]);
    }

    public function updateImage($user_id, $image_url) {
        $stmt = $this->connection->prepare("
            UPDATE users SET image_url = :img WHERE user_id = :uid
        ");
        return $stmt->execute([
            'img' => $image_url,
            'uid' => $user_id
        ]);
    }

    public function deleteUser($user_id) {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE user_id = :id");
        return $stmt->execute(['id' => $user_id]);
    }

    public function getAllUsersWithRoles() {
        $query = "SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id";
        return $this->query($query);
    }

    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE user_id = :user_id";
        return $this->query($query, ['user_id' => $userId]);
    }

    public function updateUser($userId, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");
        $query = "UPDATE users SET $fields WHERE user_id = :user_id";
        $data['user_id'] = $userId;
        return $this->query($query, $data);
    }

    public function getUserByIdWithRole($userId) {
        $query = "
            SELECT u.*, u.role_id AS role_id
            FROM users u
            WHERE u.user_id = :user_id
        ";
        $result = $this->query($query, ['user_id' => $userId]);
        error_log('Query result for getUserByIdWithRole: ' . json_encode($result));
        return $result[0] ?? null;
    }

}
