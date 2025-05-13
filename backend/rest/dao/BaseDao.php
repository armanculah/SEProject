<?php
require_once __DIR__ . '/config.php';

class BaseDao {
    protected $table;
    protected $primaryKey;
    protected $connection;

    public function __construct($table, $primaryKey = 'id') {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->connection = Database::connect();
    }

    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = :id";
        $stmt = $this->connection->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getByField($field, $value) {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} WHERE {$field} = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function query($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
