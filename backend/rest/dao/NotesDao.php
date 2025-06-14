<?php
require_once __DIR__ . '/BaseDao.php';

class NotesDao extends BaseDao {

    public function __construct() {
        parent::__construct("notes", "note_id");
    }

    public function getNotesByProductId($product_id) {
        $stmt = $this->connection->prepare("
            SELECT n.note_id, n.note_name
            FROM notes n
            JOIN product_note pn ON n.note_id = pn.note_id
            WHERE pn.product_id = :product_id
        ");
        $stmt->execute(['product_id' => $product_id]);
        return $stmt->fetchAll();
    }

    public function getNoteByName($note_name) {
        $stmt = $this->connection->prepare("SELECT * FROM notes WHERE note_name = :name");
        $stmt->execute(['name' => $note_name]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->connection->prepare("INSERT INTO notes (note_name) VALUES (:note_name)");
        $stmt->bindParam(':note_name', $data['note_name']);
        $stmt->execute();
        return $this->connection->lastInsertId();
    }

    public function delete($noteId) {
        $stmt = $this->connection->prepare("DELETE FROM notes WHERE note_id = :note_id");
        $stmt->bindParam(':note_id', $noteId);
        return $stmt->execute();
    }
}
