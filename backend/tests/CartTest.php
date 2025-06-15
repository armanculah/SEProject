<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../rest/services/CartService.php';
require_once __DIR__ . '/../rest/services/ProductService.php';
require_once __DIR__ . '/../rest/services/UserService.php';
require_once __DIR__ . '/../rest/dao/CategoryDao.php';

class CartTest extends TestCase
{
    private $cartService;
    private $productService;
    private $userService;
    private $categoryDao;
    private $testCategoryId;
    private $testUserId;
    private $testProductId;

    protected function setUp(): void
    {
        $this->cartService = new CartService();
        $this->productService = new ProductService();
        $this->userService = new UserService();
        $this->categoryDao = new CategoryDao();
        // Create test category
        $categoryName = 'Test Category ' . time();
        $category = [ 'name' => $categoryName ];
        $createdCategory = $this->categoryDao->insert('category', $category);
        $this->testCategoryId = $createdCategory['id'];
        // Create test user
        $userData = [
            'full_name' => 'Cart User',
            'address' => 'Cart Street',
            'username' => 'cartuser_' . time(),
            'date_of_birth' => '2000-01-01',
            'email' => 'cartuser_' . time() . '@example.com',
            'password' => 'cartpass123',
            'role' => 'customer'
        ];
        $createdUser = $this->userService->add_user($userData);
        $this->testUserId = $createdUser['id'];
        // Create test product
        $productData = [
            'name' => 'Cart Product',
            'description' => 'Cart Product Description',
            'price_each' => 50.0,
            'quantity' => 100,
            'category_id' => $this->testCategoryId
        ];
        $createdProduct = $this->productService->add_product($productData);
        $this->testProductId = $createdProduct['id'];
    }

    protected function tearDown(): void
    {
        // Clean up cart
        $this->cartService->remove_from_cart($this->testUserId, $this->testProductId);
        // Clean up product
        $this->productService->delete_product($this->testProductId);
        // Clean up user
        $this->userService->delete_user($this->testUserId);
        // Clean up category
        $this->categoryDao->delete('category', $this->testCategoryId);
    }

    public function testAddToCartAndCartOperations()
    {
        // Add to cart
        $addResult = $this->cartService->add_to_cart($this->testUserId, $this->testProductId, 2);
        $this->assertIsArray($addResult);
        $this->assertEquals('success', $addResult['status']);
        // Verify cart contents
        $cart = $this->cartService->get_cart_by_user($this->testUserId);
        $this->assertNotEmpty($cart);
        $this->assertEquals($this->testProductId, $cart[0]['product_id']);
        $this->assertEquals(2, $cart[0]['cart_quantity']);
        // Update quantity
        $this->cartService->update_quantity($this->testUserId, $this->testProductId, 5);
        $cart = $this->cartService->get_cart_by_user($this->testUserId);
        $this->assertEquals(5, $cart[0]['cart_quantity']);
        // Remove from cart
        $this->cartService->remove_from_cart($this->testUserId, $this->testProductId);
        $cart = $this->cartService->get_cart_by_user($this->testUserId);
        $this->assertEmpty($cart);
    }
} 