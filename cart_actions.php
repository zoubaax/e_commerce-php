<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to manage your cart';
    redirect('login.php');
}

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Invalid request';
        redirect('cart.php');
    }
}

$action = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? filter_var($_POST['product_id'], FILTER_VALIDATE_INT) : 0;
$quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : 1;

if ($product_id === false || $quantity === false) {
    $_SESSION['error'] = 'Invalid input';
    redirect('cart.php');
}

switch ($action) {
    case 'add':
        // Check if product exists and is in stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            $_SESSION['error'] = 'Product not found';
            redirect('cart.php');
        }

        if ($product['stock'] < $quantity) {
            $_SESSION['error'] = 'Not enough stock available';
            redirect('cart.php');
        }

        // Check if product is already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Update quantity
            $new_quantity = $cart_item['quantity'] + $quantity;
            if ($new_quantity > $product['stock']) {
                $_SESSION['error'] = 'Not enough stock available';
                redirect('cart.php');
            }
            
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_quantity, $cart_item['id']]);
        } else {
            // Add new item to cart
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
        }

        $_SESSION['success'] = 'Product added to cart';
        break;

    case 'update':
        if ($quantity < 1) {
            $_SESSION['error'] = 'Quantity must be at least 1';
            redirect('cart.php');
        }

        // Check stock
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($quantity > $product['stock']) {
            $_SESSION['error'] = 'Not enough stock available';
            redirect('cart.php');
        }

        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $_SESSION['user_id'], $product_id]);
        $_SESSION['success'] = 'Cart updated';
        break;

    case 'remove':
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $_SESSION['success'] = 'Product removed from cart';
        break;

    case 'clear':
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $_SESSION['success'] = 'Cart cleared';
        break;

    default:
        $_SESSION['error'] = 'Invalid action';
}

redirect('cart.php');
?>
