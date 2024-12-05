<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('No image ID specified');
}

try {
    $stmt = $pdo->prepare("SELECT image, image_type FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();

    if (!$product || !$product['image'] || !$product['image_type']) {
        http_response_code(404);
        exit('Image not found');
    }

    // Set the content type header
    header('Content-Type: ' . $product['image_type']);
    
    // Output the image data
    echo $product['image'];
} catch (Exception $e) {
    error_log("Error retrieving image: " . $e->getMessage());
    http_response_code(500);
    exit('Error retrieving image');
}
