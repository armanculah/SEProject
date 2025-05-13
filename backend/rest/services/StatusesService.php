<?php
require_once 'BaseService.php';
require_once '../dao/StatusesDao.php';

class StatusesService extends BaseService {
    private $statusesDao;

    public function __construct() {
        $this->statusesDao = new StatusesDao();
    }

    public function getAllStatuses() {
        return $this->statusesDao->getAll();
    }

    public function getStatusById($statusId) {
        return $this->statusesDao->getById($statusId);
    }

    public function createStatus($data) {
        return $this->statusesDao->insert($data);
    }

    public function updateStatus($statusId, $data) {
        return $this->statusesDao->update($statusId, $data);
    }

    public function deleteStatus($statusId) {
        return $this->statusesDao->delete($statusId);
    }
}
?>
