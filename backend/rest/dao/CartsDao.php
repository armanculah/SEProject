<?php
require_once __DIR__ . '/BaseDao.php';

class CartsDao extends BaseDao {
    public function __construct() {
    
        parent::__construct("carts", "cart_id");
    }

    public function getCartByUserId($userId) {
        $query = "SELECT * FROM carts WHERE user_id = :user_id";
        return $this->query($query, ['user_id' => $userId]);
    }

    public function removeFromCart($userId, $productId) {
        $stmt = $this->connection->prepare("DELETE FROM carts WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function updateCartQuantity($userId, $productId, $quantity) {
        $stmt = $this->connection->prepare("UPDATE carts SET cart_quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function clearCart($userId) {
        $stmt = $this->connection->prepare("DELETE FROM carts WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function getByField($field, $value) {
        $query = "SELECT product_id, quantity FROM products WHERE $field = :value";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteByUserIdAndItemId($userId, $itemId) {
        $query = "DELETE FROM carts WHERE user_id = :user_id AND cart_id = :cart_id";
        $this->query($query, ['user_id' => $userId, 'cart_id' => $itemId]);
    }

    public function addToCart($userId, $productId, $cartQuantity) {
        $query = "INSERT INTO carts (user_id, product_id, cart_quantity) VALUES (:user_id, :product_id, :cart_quantity)";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':cart_quantity', $cartQuantity);
        return $stmt->execute();
    }
}
?>
