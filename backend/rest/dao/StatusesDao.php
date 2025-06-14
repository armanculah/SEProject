<?php
require_once __DIR__ . '/BaseDao.php';

class StatusesDao extends BaseDao {

    public function __construct() {
        parent::__construct("statuses", "status_id");
    }

    public function getStatusByName($status_name) {
        $stmt = $this->connection->prepare("SELECT * FROM statuses WHERE status_name = :name");
        $stmt->execute(['name' => $status_name]);
        return $stmt->fetch();
    }
}
