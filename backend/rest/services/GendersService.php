<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/GendersDao.php';

class GendersService extends BaseService {
    private $gendersDao;

    public function __construct() {
        $this->gendersDao = new GendersDao();
    }

    public function getAllGenders() {
        return $this->gendersDao->getAll();
    }

    public function getGenderById($genderId) {
        return $this->gendersDao->getById($genderId);
    }

    public function createGender($data) {
        return $this->gendersDao->insert($data);
    }

    public function updateGender($genderId, $data) {
        return $this->gendersDao->update($genderId, $data);
    }

    public function deleteGender($genderId) {
        return $this->gendersDao->delete($genderId);
    }
}
?>
