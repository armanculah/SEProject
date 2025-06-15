<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../rest/services/ProductService.php';
require_once __DIR__ . '/../rest/dao/CategoryDao.php';

class ProductTest extends TestCase
{
    private $productService;
    private $categoryDao;
    private $testCategoryId;

    protected function setUp(): void
    {
        $this->productService = new ProductService();
        $this->categoryDao = new CategoryDao();
        // Ensure a test category exists
        $categoryName = 'Test Category ' . time();
        $category = [ 'name' => $categoryName ];
        $created = $this->categoryDao->insert('category', $category);
        $this->testCategoryId = $created['id'];
    }

    protected function tearDown(): void
    {
        // Clean up test category
        $this->categoryDao->delete('category', $this->testCategoryId);
    }

    public function testAddProduct()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price_each' => 99.99,
            'quantity' => 10,
            'category_id' => $this->testCategoryId
        ];
        $created = $this->productService->add_product($productData);
        $this->assertIsArray($created);
        $this->assertArrayHasKey('id', $created);
        $productId = $created['id'];
        // Clean up
        $this->productService->delete_product($productId);
    }

    public function testGetProductById()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price_each' => 99.99,
            'quantity' => 10,
            'category_id' => $this->testCategoryId
        ];
        $created = $this->productService->add_product($productData);
        $productId = $created['id'];
        $product = $this->productService->get_product_by_id($productId);
        $this->assertEquals($productData['name'], $product['name']);
        $this->assertEquals($productData['description'], $product['description']);
        $this->assertEquals($productData['price_each'], $product['price_each']);
        $this->assertEquals($productData['quantity'], $product['quantity']);
        $this->assertEquals('Test Category ' . date('Y'), substr($product['category'], 0, 15));
        // Clean up
        $this->productService->delete_product($productId);
    }

    public function testUpdateProduct()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price_each' => 99.99,
            'quantity' => 10,
            'category_id' => $this->testCategoryId
        ];
        $created = $this->productService->add_product($productData);
        $productId = $created['id'];
        $updateData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'price_each' => 149.99,
            'quantity' => 5,
            'category_id' => $this->testCategoryId
        ];
        $updated = $this->productService->update_product($productId, $updateData);
        $this->assertIsArray($updated);
        $product = $this->productService->get_product_by_id($productId);
        $this->assertEquals($updateData['name'], $product['name']);
        $this->assertEquals($updateData['description'], $product['description']);
        $this->assertEquals($updateData['price_each'], $product['price_each']);
        $this->assertEquals($updateData['quantity'], $product['quantity']);
        // Clean up
        $this->productService->delete_product($productId);
    }

    public function testDeleteProduct()
    {
        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price_each' => 99.99,
            'quantity' => 10,
            'category_id' => $this->testCategoryId
        ];
        $created = $this->productService->add_product($productData);
        $productId = $created['id'];
        $this->productService->delete_product($productId);
        $product = $this->productService->get_product_by_id($productId);
        $this->assertEmpty($product);
    }
} 