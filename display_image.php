<?php
require_once 'config/config.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT image, image_type FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
    
    if ($product && $product['image']) {
        header("Content-Type: " . $product['image_type']);
        echo $product['image'];
        exit;
    }
}

// If no image found or error, display a default image
header("Content-Type: image/png");
readfile("assets/img/no-image.png");
