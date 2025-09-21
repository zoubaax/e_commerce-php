<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('Please login to view order details');
}

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Debug logging
error_log("Fetching order ID: " . $order_id . " for user: " . $_SESSION['user_id']);

// Verify that this order belongs to the current user
$stmt = $pdo->prepare("
    SELECT o.*, u.full_name, u.email, u.phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ? AND o.user_id = ?
");

try {
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if (!$order) {
        error_log("Order not found or doesn't belong to user");
        die('Order not found');
    }

    // Get order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die('Error retrieving order details');
}
?>

<div class="order-details">
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="fw-bold">Order Information</h6>
            <p class="text-muted">
                <strong>Order ID:</strong> #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?><br>
                <strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?><br>
                <strong>Status:</strong> <span class="badge bg-<?php echo $order['status'] === 'delivered' ? 'success' : 'primary'; ?>"><?php echo ucfirst($order['status']); ?></span><br>
                <strong>Payment Method:</strong> <?php echo $order['payment_method']; ?>
            </p>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold">Customer Information</h6>
            <p class="text-muted">
                <strong>Name:</strong> <?php echo clean($order['full_name']); ?><br>
                <strong>Email:</strong> <?php echo clean($order['email']); ?><br>
                <strong>Phone:</strong> <?php echo clean($order['phone']); ?><br>
            </p>
        </div>
    </div>

    <h6 class="fw-bold">Shipping Address</h6>
    <p class="mb-4 text-muted"><?php echo nl2br(clean($order['shipping_address'])); ?></p>

    <h6 class="fw-bold">Order Items</h6>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="get_image.php?id=<?php echo $item['product_id']; ?>" 
                                         alt="<?php echo clean($item['name']); ?>"
                                         class="me-2 rounded"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
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
