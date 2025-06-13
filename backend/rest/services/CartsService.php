<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CartsDao.php';
require_once __DIR__ . '/../dao/ProductsDao.php'; // Include ProductsDao

class CartsService extends BaseService {
    private $cartsDao;

    public function __construct() {
        $this->cartsDao = new CartsDao();
    }

    public function getCartByUserId($userId) {
        return $this->cartsDao->getCartByUserId($userId);
    }

    public function addToCart($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            throw new Exception("Quantity must be greater than zero.");
        }

        // Ensure the product exists
        $product = $this->cartsDao->getByField('product_id', $productId);
        if (empty($product)) {
            throw new Exception("Product does not exist.");
        }

        if ($product[0]['quantity'] < $quantity) {
            throw new Exception("Insufficient quantity for product.");
        }

        $cartItems = $this->cartsDao->getByField('user_id', $userId);
        foreach ($cartItems as $item) {
            if ($item['product_id'] == $productId) {
                $newQuantity = $item['cart_quantity'] + $quantity;

                return $this->cartsDao->updateCartQuantity($userId, $productId, $newQuantity);
            }
        }

        $data = [
            'user_id' => $userId,
            'product_id' => $productId,
            'cart_quantity' => $quantity
        ];
        return $this->cartsDao->insert($data);
    }

    public function removeFromCart($userId, $productId) {
        return $this->cartsDao->removeFromCart($userId, $productId);
    }

    public function updateCartQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            throw new Exception("Quantity must be greater than zero.");
        }
        return $this->cartsDao->updateCartQuantity($userId, $productId, $quantity);
    }

    public function clearCart($userId) {
        return $this->cartsDao->clearCart($userId);
    }

    public function getCartTotal($userId) {
        $cartItems = $this->cartsDao->getCartByUserId($userId);
        $total = 0;
        foreach ($cartItems as $item) {
            $product = $this->cartsDao->getByField('product_id', $item['product_id']);
            if (!empty($product)) {
                $total += $product[0]['price'] * $item['cart_quantity'];
            }
        }
        return $total;
    }

    // Removed redundant methods `getCart`, `addItem`, and `removeItem`.
}
?>
