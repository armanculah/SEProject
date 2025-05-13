<?php
require_once '../config.php';


try {
    $conn = Database::connect();
    if ($conn) {
        echo "Successfully connected to the database!";
    } else {
        echo "Connection failed!";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
