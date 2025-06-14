<?php
require_once __DIR__ . '/BaseDao.php';

class ProductsDao extends BaseDao {

    public function __construct() {
        parent::__construct("products", "product_id");
    }

    public function getAllWithGender() {
        $stmt = $this->connection->prepare("
            SELECT p.*, g.gender_name 
            FROM products p
            JOIN genders g ON p.gender_id = g.gender_id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByName($product_name) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE product_name = :name");
        $stmt->execute(['name' => $product_name]);
        return $stmt->fetch();
    }

    public function updateProduct($product_id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE products SET $fields WHERE product_id = :product_id";
        $stmt = $this->connection->prepare($sql);
        $data['product_id'] = $product_id;
        return $stmt->execute($data);
    }

    public function deleteProduct($product_id) {
        $stmt = $this->connection->prepare("DELETE FROM products WHERE product_id = :id");
        return $stmt->execute(['id' => $product_id]);
    }

    public function getAllWithGenderAndNotes() {
        $stmt = $this->connection->prepare(
            "SELECT p.*, g.gender_name, GROUP_CONCAT(n.note_name SEPARATOR ', ') AS notes
             FROM products p
             LEFT JOIN genders g ON p.gender_id = g.gender_id
             LEFT JOIN product_note pn ON p.product_id = pn.product_id
             LEFT JOIN notes n ON pn.note_id = n.note_id
             GROUP BY p.product_id"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function filterProducts($filters) {
        $query = "SELECT p.*, g.gender_name FROM products p LEFT JOIN genders g ON p.gender_id = g.gender_id WHERE 1=1";
        $params = [];

        if (!empty($filters['gender_id'])) {
            $query .= " AND p.gender_id = :gender_id";
            $params['gender_id'] = $filters['gender_id'];
        }

        if (!empty($filters['price_min'])) {
            $query .= " AND p.price >= :price_min";
            $params['price_min'] = $filters['price_min'];
        }

        if (!empty($filters['price_max'])) {
            $query .= " AND p.price <= :price_max";
            $params['price_max'] = $filters['price_max'];
        }

        return $this->query($query, $params);
    }

    public function searchByName($name) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM products WHERE product_name LIKE :name"
        );
        $stmt->execute(['name' => "%$name%"]);
        return $stmt->fetchAll();
    }

    public function getOnSale() {
        $stmt = $this->connection->prepare(
            "SELECT * FROM products WHERE on_sale = 1"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateImage($productId, $imageUrl) {
        $stmt = $this->connection->prepare(
            "UPDATE products SET image_url = :image_url WHERE product_id = :product_id"
        );
        $stmt->execute(['image_url' => $imageUrl, 'product_id' => $productId]);
        return $stmt->rowCount();
    }

    public function getFiltered($filters) {
        $query = "SELECT * FROM products WHERE 1=1";

        $params = [];
        if (isset($filters['category'])) {
            $query .= " AND category = :category";
            $params['category'] = $filters['category'];
        }
        if (isset($filters['gender'])) {
            $query .= " AND gender = :gender";
            $params['gender'] = $filters['gender'];
        }

        return $this->query($query, $params);
    }

    public function getProductsByCategory($categoryId) {
        if (empty($noteNames) || !is_array($noteNames)) {
            return []; // Return an empty result set if no notes are provided
        }
        $placeholders = implode(',', array_fill(0, count($noteNames), '?'));
        $query = "
            SELECT p.*, g.gender_name, GROUP_CONCAT(n.note_name SEPARATOR ', ') AS notes
            FROM products p
            LEFT JOIN genders g ON p.gender_id = g.gender_id
            LEFT JOIN product_note pn ON p.product_id = pn.product_id
            LEFT JOIN notes n ON pn.note_id = n.note_id
            WHERE p.product_id IN (
                SELECT DISTINCT pn.product_id
                FROM product_note pn
                LEFT JOIN notes n ON pn.note_id = n.note_id
                WHERE n.note_name IN ($placeholders)
            )
            GROUP BY p.product_id
        ";
        $params = $noteNames;
        return $this->query($query, $params);
    }

    public function searchByGenderAndNote($genderId, $noteName) {
        $query = "
            SELECT p.*
            FROM products p
            LEFT JOIN genders g ON p.gender_id = g.gender_id
            LEFT JOIN product_note pn ON p.product_id = pn.product_id
            LEFT JOIN notes n ON pn.note_id = n.note_id
            WHERE g.gender_id = :gender_id AND n.note_name = :note_name
        ";
        error_log('Searching products with gender_id: ' . $genderId . ' and note_name: ' . $noteName);
        error_log('Executing query: ' . $query);
        $results = $this->query($query, ['gender_id' => $genderId, 'note_name' => $noteName]);
        error_log('Query results: ' . json_encode($results));
        return $results;
    }

    public function searchByGenderAndMultipleNotes($genderId, $noteNames) {
        $placeholders = implode(',', array_fill(0, count($noteNames), '?'));
        $query = "
            SELECT p.*
            FROM products p
            LEFT JOIN genders g ON p.gender_id = g.gender_id
            LEFT JOIN product_note pn ON p.product_id = pn.product_id
            LEFT JOIN notes n ON pn.note_id = n.note_id
            WHERE g.gender_id = ? AND n.note_name IN ($placeholders)
            GROUP BY p.product_id
            HAVING COUNT(DISTINCT n.note_name) = ?
        ";
        $params = array_merge([$genderId], $noteNames, [count($noteNames)]);
        return $this->query($query, $params);
    }

    public function searchProducts($filters) {
        $query = "
            SELECT p.*, g.gender_name, GROUP_CONCAT(n.note_name SEPARATOR ', ') AS notes
            FROM products p
            LEFT JOIN genders g ON p.gender_id = g.gender_id
            LEFT JOIN product_note pn ON p.product_id = pn.product_id
            LEFT JOIN notes n ON pn.note_id = n.note_id
            WHERE 1=1
        ";

        $params = [];

        // Gender filter
        if (!empty($filters['gender'])) {
            if (in_array('male', $filters['gender']) && in_array('female', $filters['gender'])) {
                $query .= " AND g.gender_name = 'unisex'";
            } elseif (in_array('male', $filters['gender'])) {
                $query .= " AND (g.gender_name = 'male' OR g.gender_name = 'unisex')";
            } elseif (in_array('female', $filters['gender'])) {
                $query .= " AND (g.gender_name = 'female' OR g.gender_name = 'unisex')";
            }
        }

        // Notes filter
        if (!empty($filters['notes'])) {
            $placeholders = implode(',', array_fill(0, count($filters['notes']), '?'));
            $query .= " AND p.product_id IN (
                SELECT pn.product_id
                FROM product_note pn
                LEFT JOIN notes n ON pn.note_id = n.note_id
                WHERE n.note_name IN ($placeholders)
                GROUP BY pn.product_id
                HAVING COUNT(DISTINCT n.note_name) = ?
            )";
            $params = array_merge($params, $filters['notes'], [count($filters['notes'])]);
        }

        // On Sale filter
        if (!empty($filters['on_sale'])) {
            $query .= " AND p.on_sale = 1";
        }

        $query .= " GROUP BY p.product_id";

        error_log('Executing query: ' . $query);
        error_log('Query parameters: ' . json_encode($params));
        $results = $this->query($query, $params);
        error_log('Query results: ' . json_encode($results));
        return $results;
    }
}
