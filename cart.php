<?php
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to view your cart';
    redirect('login.php');
}

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.stock, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4">Shopping Cart</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">
                Your cart is empty. <a href="products.php">Continue shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <img src="<?php echo !empty($item['image']) ? 'get_image.php?id=' . $item['product_id'] : 'assets/images/placeholder.jpg'; ?>" 
                                         class="img-fluid rounded-start" 
                                         alt="<?php echo clean($item['name']); ?>">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title"><?php echo clean($item['name']); ?></h5>
                                            <form action="cart_actions.php" method="POST" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <p class="card-text">
                                            Price: $<?php echo number_format($item['price'], 2); ?>
                                        </p>
                                        <form action="cart_actions.php" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                            <div class="input-group" style="width: 150px;">
                                                <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input').stepDown()">-</button>
                                                <input type="number" class="form-control text-center" name="quantity" 
                                                       value="<?php echo $item['quantity']; ?>" 
                                                       min="1" max="<?php echo $item['stock']; ?>">
                                                <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input').stepUp()">+</button>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary ms-2">Update</button>
                                        </form>
                                        <p class="card-text mt-2">
                                            Subtotal: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>$<?php echo number_format($total, 2); ?></strong>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                                <form action="cart_actions.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-outline-danger w-100">Clear Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>
