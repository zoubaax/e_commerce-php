<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

echo "<pre>";

// Check database connection
echo "Testing database connection...\n";
try {
    $pdo->query("SELECT 1");
    echo "Database connection successful!\n\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n\n";
    exit;
}

// Check products table structure
echo "Products table structure:\n";
try {
    $stmt = $pdo->query("SHOW CREATE TABLE products");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $result['Create Table'] . "\n\n";
} catch (PDOException $e) {
    echo "Error getting table structure: " . $e->getMessage() . "\n\n";
}

// Check categories
echo "Available categories:\n";
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "\n";
} catch (PDOException $e) {
    echo "Error getting categories: " . $e->getMessage() . "\n\n";
}

// Check existing products
echo "Existing products:\n";
try {
    $stmt = $pdo->query("SELECT * FROM products");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "\n";
} catch (PDOException $e) {
    echo "Error getting products: " . $e->getMessage() . "\n\n";
}

echo "</pre>";
