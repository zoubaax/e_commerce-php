<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to proceed with checkout';
    redirect('login.php');
}

// Fetch cart items with product details
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

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <?php if (empty($cart_items)): ?>
            <div class="alert alert-info">
                Your cart is empty. Please add some products before checking out.
                <a href="products.php" class="btn btn-primary ms-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Shipping Information</h4>
                        </div>
                        <div class="card-body">
                            <form action="process_order.php" method="POST" id="checkout-form">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo clean($user['full_name']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo clean($user['email']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo clean($user['phone']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Shipping Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo clean($user['address']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cod">Cash on Delivery</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="credit_card">Credit Card</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Order Summary</h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="mb-0"><?php echo clean($item['name']); ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <div>
                                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Subtotal</h5>
                                <h5 class="mb-0">$<?php echo number_format($total, 2); ?></h5>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Shipping</h5>
                                <h5 class="mb-0">Free</h5>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Total</h4>
                                <h4 class="mb-0 text-primary">$<?php echo number_format($total, 2); ?></h4>
                            </div>
                            
                            <button type="submit" form="checkout-form" class="btn btn-primary w-100">
                                Place Order
                            </button>
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
