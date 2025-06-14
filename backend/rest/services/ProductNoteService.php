<?php
require_once __DIR__ . '/BaseService.php';
require_once '../dao/ProductNoteDao.php';

class ProductNoteService extends BaseService {
    private $productNoteDao;

    public function __construct() {
        $this->productNoteDao = new ProductNoteDao();
    }

    public function getNotesByProductId($productId) {
        return $this->productNoteDao->getNotesByProductId($productId);
    }

    public function addNoteToProduct($productId, $noteId) {
        return $this->productNoteDao->addNoteToProduct($productId, $noteId);
    }

    public function removeNoteFromProduct($productId, $noteId) {
        return $this->productNoteDao->removeNoteFromProduct($productId, $noteId);
    }
}
?>
