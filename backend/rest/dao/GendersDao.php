<?php
require_once __DIR__ . '/BaseDao.php';

class GendersDao extends BaseDao {

    public function __construct() {
        parent::__construct("genders", "gender_id");
    }

    public function getGenderByName($gender_name) {
        $stmt = $this->connection->prepare("SELECT * FROM genders WHERE gender_name = :name");
        $stmt->execute(['name' => $gender_name]);
        return $stmt->fetch();
    }
}
