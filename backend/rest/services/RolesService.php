<?php
require_once 'BaseService.php';
require_once '../dao/RolesDao.php';

class RolesService extends BaseService {
    private $rolesDao;

    public function __construct() {
        $this->rolesDao = new RolesDao();
    }

    public function getAllRoles() {
        return $this->rolesDao->getAll();
    }

    public function getRoleById($roleId) {
        return $this->rolesDao->getById($roleId);
    }

    public function createRole($data) {
        return $this->rolesDao->insert($data);
    }

    public function updateRole($roleId, $data) {
        return $this->rolesDao->update($roleId, $data);
    }

    public function deleteRole($roleId) {
        return $this->rolesDao->delete($roleId);
    }
}
?>
