<?php
require_once __DIR__ . '/BaseDao.php';

class PaymentsDao extends BaseDao {

    public function __construct() {
        parent::__construct("payments", "payment_id");
    }

    public function getByOrderId($order_id) {
        $stmt = $this->connection->prepare("SELECT * FROM payments WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $order_id]);
        return $stmt->fetch();
    }

    public function updateStatus($payment_id, $status) {
        $stmt = $this->connection->prepare("
            UPDATE payments SET payment_status = :status WHERE payment_id = :payment_id
        ");
        return $stmt->execute([
            'status' => $status,
            'payment_id' => $payment_id
        ]);
    }

    public function getAllWithOrders() {
        $stmt = $this->connection->prepare("
            SELECT p.*, o.order_date, o.total_price
            FROM payments p
            JOIN orders o ON p.order_id = o.order_id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
