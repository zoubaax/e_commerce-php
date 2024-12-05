<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to proceed with checkout';
    redirect('login.php');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid request';
    redirect('checkout.php');
}

// Validate required fields
$required_fields = ['full_name', 'email', 'phone', 'address', 'payment_method'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = 'Please fill in all required fields';
        redirect('checkout.php');
    }
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Get cart items
    $stmt = $pdo->prepare("
        SELECT c.*, p.price, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        throw new Exception('Your cart is empty');
    }

    // Calculate total and check stock
    $total = 0;
    foreach ($cart_items as $item) {
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Not enough stock for some items");
        }
        $total += $item['price'] * $item['quantity'];
    }

    // Create order
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method) 
        VALUES (?, ?, 'pending', ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $total,
        clean($_POST['address']),
        clean($_POST['payment_method'])
    ]);
    $order_id = $pdo->lastInsertId();

    // Create order items and update stock
    foreach ($cart_items as $item) {
        // Add to order items
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);

        // Update stock
        $stmt = $pdo->prepare("
            UPDATE products 
            SET stock = stock - ? 
            WHERE id = ?
        ");
        $stmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    // Update user information
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, phone = ?, address = ? 
        WHERE id = ?
    ");
    $stmt->execute([
        clean($_POST['full_name']),
        clean($_POST['email']),
        clean($_POST['phone']),
        clean($_POST['address']),
        $_SESSION['user_id']
    ]);

    // Commit transaction
    $pdo->commit();

    $_SESSION['success'] = 'Order placed successfully! Order ID: ' . $order_id;
    redirect('order_confirmation.php?id=' . $order_id);

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    $_SESSION['error'] = 'Error processing order: ' . $e->getMessage();
    redirect('checkout.php');
}
