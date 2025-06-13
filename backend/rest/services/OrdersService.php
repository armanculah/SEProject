<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrdersDao.php';

class OrdersService extends BaseService {
    private $ordersDao;

    public function __construct() {
        $this->ordersDao = new OrdersDao();
    }

    public function getAllOrders() {
        return $this->ordersDao->getAll();
    }

    public function getOrderById($orderId) {
        return $this->ordersDao->getById($orderId);
    }

    public function createOrder($userId, $cartItems, $totalPrice) {
        // Insert the order
        $orderData = [
            'user_id' => $userId,
            'status_id' => 1, // Default status: Pending
            'total_price' => $totalPrice
        ];
        $orderId = $this->ordersDao->insert($orderData);

        // Add items to the order
        foreach ($cartItems as $item) {
            $this->ordersDao->addItemToOrder($orderId, $item['product_id'], $item['quantity'], $item['price']);
        }

        return $orderId;
    }

    public function updateOrderStatus($orderId, $statusId) {
        return $this->ordersDao->updateStatus($orderId, $statusId);
    }

    public function getOrdersByUserId($userId) {
        return $this->ordersDao->getOrdersByUserId($userId);
    }

    public function deleteOrder($orderId) {
        return $this->ordersDao->delete($orderId);
    }
}
?>