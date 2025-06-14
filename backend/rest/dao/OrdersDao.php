<?php
require_once __DIR__ . '/BaseDao.php';

class OrdersDao extends BaseDao {

    public function __construct() {
        parent::__construct("orders", "order_id");
    }

    public function getAllWithUserAndStatus() {
        $query = "
            SELECT o.*, u.user_name, s.status_name
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            JOIN statuses s ON o.status_id = s.status_id
        ";
        return $this->query($query);
    }

    public function getOrdersByUserId($user_id) {
        $query = "SELECT * FROM orders WHERE user_id = :user_id";
        return $this->query($query, ['user_id' => $user_id]);
    }

    public function updateStatus($order_id, $status_id) {
        $query = "
            UPDATE orders SET status_id = :status_id WHERE order_id = :order_id
        ";
        return $this->query($query, [
            'status_id' => $status_id,
            'order_id' => $order_id
        ]);
    }

    public function addItemToOrder($orderId, $productId, $quantity, $price) {
        $query = "INSERT INTO item_in_order (order_id, product_id, quantity, item_price) VALUES (:order_id, :product_id, :quantity, :price)";
        return $this->query($query, [
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ]);
    }

    public function createOrder($data) {
        $query = "INSERT INTO orders (user_id, order_address, order_city, order_country, order_phone, order_date, status_id, total_price) VALUES (:user_id, :order_address, :order_city, :order_country, :order_phone, :order_date, :status_id, :total_price)";
        $this->query($query, $data);
        return $this->connection->lastInsertId();
    }
}
