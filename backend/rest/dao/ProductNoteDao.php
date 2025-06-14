<?php
require_once __DIR__ . '/BaseDao.php';

class ProductNoteDao extends BaseDao {
    public function __construct() {
        parent::__construct("product_note"); // composite keys handled manually
    }

    public function getNotesByProductId($product_id) {
        $stmt = $this->connection->prepare("
            SELECT n.* FROM product_note pn
            JOIN notes n ON pn.note_id = n.note_id
            WHERE pn.product_id = :pid
        ");
        $stmt->execute(['pid' => $product_id]);
        return $stmt->fetchAll();
    }

    public function addNoteToProduct($product_id, $note_id) {
        $stmt = $this->connection->prepare("
            INSERT INTO product_note (product_id, note_id)
            VALUES (:pid, :nid)
        ");
        return $stmt->execute(['pid' => $product_id, 'nid' => $note_id]);
    }

    public function removeNoteFromProduct($product_id, $note_id) {
        $stmt = $this->connection->prepare(
            "DELETE FROM product_note WHERE product_id = :pid AND note_id = :nid"
        );
        return $stmt->execute(['pid' => $product_id, 'nid' => $note_id]);
    }

}
?>