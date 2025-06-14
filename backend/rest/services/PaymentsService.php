<?php
require_once __DIR__ . '/BaseService.php';
require_once '../dao/PaymentsDao.php';

class PaymentsService extends BaseService {
    private $paymentsDao;

    public function __construct() {
        $this->paymentsDao = new PaymentsDao();
    }

    public function getAllPayments() {
        return $this->paymentsDao->getAll();
    }

    public function getPaymentById($paymentId) {
        return $this->paymentsDao->getById($paymentId);
    }

    public function createPayment($data) {
        return $this->paymentsDao->insert($data);
    }

    public function updatePayment($paymentId, $data) {
        return $this->paymentsDao->update($paymentId, $data);
    }

    public function deletePayment($paymentId) {
        return $this->paymentsDao->delete($paymentId);
    }

    public function getPaymentsByOrderId($orderId) {
        return $this->paymentsDao->getByField('order_id', $orderId);
    }

    public function updatePaymentStatus($paymentId, $status) {
        return $this->paymentsDao->updateStatus($paymentId, $status);
    }
}
?>
