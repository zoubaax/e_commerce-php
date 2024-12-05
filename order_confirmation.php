<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to view order details';
    redirect('login.php');
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if (!$order_id) {
    $_SESSION['error'] = 'Invalid order ID';
    redirect('index.php');
}

// Fetch order details
$stmt = $pdo->prepare("
    SELECT o.*, u.email, u.full_name, u.phone 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    redirect('index.php');
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h2 class="my-3">Thank You for Your Order!</h2>
                        <p class="lead">Your order has been placed successfully.</p>
                        <p>Order ID: <strong>#<?php echo $order_id; ?></strong></p>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Order Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Shipping Information</h5>
                                <p>
                                    <?php echo clean($order['full_name']); ?><br>
                                    <?php echo clean($order['shipping_address']); ?><br>
                                    Phone: <?php echo clean($order['phone']); ?><br>
                                    Email: <?php echo clean($order['email']); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5>Order Summary</h5>
                                <p>
                                    Order Status: <span class="badge bg-primary"><?php echo ucfirst($order['status']); ?></span><br>
                                    Payment Method: <?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?><br>
                                    Order Date: <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                                </p>
                            </div>
                        </div>

                        <h5>Ordered Items</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo !empty($item['image']) ? clean($item['image']) : 'assets/images/placeholder.jpg'; ?>" 
                                                         alt="<?php echo clean($item['name']); ?>" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                                         class="me-3">
                                                    <?php echo clean($item['name']); ?>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
