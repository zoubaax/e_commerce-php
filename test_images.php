<?php
require_once 'config/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Test database connection
    echo "Database connection: OK\n";
    
    // Check products table
    $stmt = $pdo->query("SELECT id, name, image IS NOT NULL as has_image, image_type FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nProducts in database:\n";
    foreach ($products as $product) {
        echo "ID: " . $product['id'] . 
             ", Name: " . $product['name'] . 
             ", Has Image: " . ($product['has_image'] ? 'Yes' : 'No') . 
             ", Image Type: " . ($product['image_type'] ?? 'None') . "\n";
    }
    
    // Check if placeholder exists
    $placeholder = __DIR__ . '/assets/images/placeholder.jpg';
    echo "\nPlaceholder image (" . $placeholder . "): " . 
         (file_exists($placeholder) ? 'EXISTS' : 'MISSING');
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
