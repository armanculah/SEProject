<?php
require_once 'BaseService.php';

require_once __DIR__ . '/../dao/ItemInOrderDao.php';

class ItemInOrderService extends BaseService {
    private $itemInOrderDao;

    public function __construct() {
        $this->itemInOrderDao = new ItemInOrderDao();
    }

    public function getItemsByOrderId($orderId) {
        return $this->itemInOrderDao->getItemsByOrderId($orderId);
    }

    public function addItemToOrder($orderId, $productId, $quantity) {
        if ($quantity <= 0) {
            throw new Exception("Quantity must be greater than zero.");
        }

        // Check if the order exists
        $order = $this->itemInOrderDao->getByField('order_id', $orderId);
        if (empty($order)) {
            throw new Exception("Order does not exist.");
        }

        // Check if the product exists
        $product = $this->itemInOrderDao->getByField('product_id', $productId);
        if (empty($product)) {
            throw new Exception("Product does not exist.");
        }

        // Consolidate quantities if the product already exists in the order
        $items = $this->itemInOrderDao->getItemsByOrderId($orderId);
        foreach ($items as $item) {
            if ($item['product_id'] == $productId) {
                $newQuantity = $item['quantity'] + $quantity;
                return $this->itemInOrderDao->updateItemQuantity($orderId, $productId, $newQuantity);
            }
        }

        // Add the item to the order
        return $this->itemInOrderDao->addItem($orderId, $productId, $quantity, $product[0]['price']);
    }

    public function removeItemFromOrder($orderId, $productId) {
        // Check if the order exists
        $order = $this->itemInOrderDao->getByField('order_id', $orderId);
        if (empty($order)) {
            throw new Exception("Order does not exist.");
        }

        return $this->itemInOrderDao->removeItem($orderId, $productId);
    }

    public function clearItemsFromOrder($orderId) {
        // Check if the order exists
        $order = $this->itemInOrderDao->getByField('order_id', $orderId);
        if (empty($order)) {
            throw new Exception("Order does not exist.");
        }

        return $this->itemInOrderDao->clearItems($orderId);
    }
}
?>
