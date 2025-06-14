<?php
require_once __DIR__ . '/BaseDao.php';

class RolesDao extends BaseDao {

    public function __construct() {
        parent::__construct("roles", "role_id");
    }

    public function getRoleByName($role_name) {
        $stmt = $this->connection->prepare("SELECT * FROM roles WHERE role_name = :name");
        $stmt->execute(['name' => $role_name]);
        return $stmt->fetch();
    }
}
