<?php
require_once __DIR__ . '/BaseDao.php';

class ItemInOrderDao extends BaseDao {
    public function __construct() {
        parent::__construct("item_in_order"); // composite keys handled manually
    }

    public function getItemsByOrderId($order_id) {
        $stmt = $this->connection->prepare("
            SELECT * FROM item_in_order WHERE order_id = :oid
        ");
        $stmt->execute(['oid' => $order_id]);
        return $stmt->fetchAll();
    }

    public function addItem($order_id, $product_id, $quantity, $price) {
        $stmt = $this->connection->prepare("
            INSERT INTO item_in_order (order_id, product_id, quantity, item_price)
            VALUES (:oid, :pid, :qty, :price)
        ");
        return $stmt->execute([
            'oid' => $order_id,
            'pid' => $product_id,
            'qty' => $quantity,
            'price' => $price
        ]);
    }

    public function removeItem($order_id, $product_id) {
        $stmt = $this->connection->prepare("
            DELETE FROM item_in_order WHERE order_id = :oid AND product_id = :pid
        ");
        return $stmt->execute(['oid' => $order_id, 'pid' => $product_id]);
    }

    public function updateItemQuantity($orderId, $productId, $quantity) {
        $stmt = $this->connection->prepare(
            "UPDATE item_in_order SET quantity = :quantity WHERE order_id = :order_id AND product_id = :product_id"
        );
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function clearItems($orderId) {
        $stmt = $this->connection->prepare(
            "DELETE FROM item_in_order WHERE order_id = :order_id"
        );
        $stmt->bindParam(':order_id', $orderId);
        return $stmt->execute();
    }
}
?>