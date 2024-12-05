<?php
require_once 'config/config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('No image ID specified');
}

try {
    $stmt = $pdo->prepare("SELECT image, image_type FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();

    if (!$product || !$product['image'] || !$product['image_type']) {
        // If no image found, serve a default placeholder
        $placeholder = 'assets/images/placeholder.jpg';
        if (file_exists($placeholder)) {
            header('Content-Type: image/jpeg');
            readfile($placeholder);
        } else {
            http_response_code(404);
            exit('Image not found');
        }
    } else {
        // Set the content type header
        header('Content-Type: ' . $product['image_type']);
        
        // Output the image data
        echo $product['image'];
    }
} catch (Exception $e) {
    error_log("Error retrieving image: " . $e->getMessage());
    http_response_code(500);
    exit('Error retrieving image');
}
