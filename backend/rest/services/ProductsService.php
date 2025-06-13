<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductsDao.php'; // Corrected path to ProductsDao.php

class ProductsService extends BaseService {
    private $productsDao;

    public function __construct() {
        $this->productsDao = new ProductsDao();
    }

    public function getAllProducts() {
        return $this->productsDao->getAllWithGenderAndNotes();
    }

    public function getProductById($productId) {
        return $this->productsDao->getById($productId);
    }

    public function createProduct($data) {
        return $this->productsDao->insert($data);
    }

    public function updateProduct($productId, $data) {
        return $this->productsDao->update($productId, $data);
    }

    public function deleteProduct($productId) {
        return $this->productsDao->delete($productId);
    }

    public function searchProductsByName($name) {
        return $this->productsDao->searchByName($name);
    }

    public function getProductsOnSale() {
        return $this->productsDao->getOnSale();
    }

    public function updateProductImage($productId, $imageUrl) {
        return $this->productsDao->updateImage($productId, $imageUrl);
    }

    public function getProductsByCategory($categoryId) {
        return $this->productsDao->getProductsByCategory($categoryId);
    }

    public function searchByGenderAndNote($genderId, $noteName) {
        return $this->productsDao->searchByGenderAndNote($genderId, $noteName);
    }

    public function searchByGenderAndMultipleNotes($genderId, $noteNames) {
        return $this->productsDao->searchByGenderAndMultipleNotes($genderId, $noteNames);
    }

    public function searchProducts($filters) {
        error_log('Filters passed to searchProducts: ' . json_encode($filters));
        return $this->productsDao->searchProducts($filters);
    }
}
?>
