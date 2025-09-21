<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo 'Order ID is required';
    exit;
}

$order_id = (int)$_GET['id'];

try {
    // Get order details with user information
    $stmt = $pdo->prepare("
        SELECT o.*, u.username, u.email, u.full_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo 'Order not found';
        exit;
    }

    // Get order items with product details
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image, p.image_type
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
?>

<div class="container-fluid p-0">
    <div class="row">
        <div class="col-md-6">
            <h6 class="mb-3">Order Information</h6>
            <table class="table table-sm">
                <tr>
                    <th>Order ID:</th>
                    <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                </tr>
                <tr>
                    <th>Date:</th>
                    <td><?php echo date('F d, Y H:i:s', strtotime($order['created_at'])); ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>
                        <?php
                        $status_class = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $class = $status_class[$order['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?php echo $class; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Total Amount:</th>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="mb-3">Customer Information</h6>
            <table class="table table-sm">
                <tr>
                    <th>Name:</th>
                    <td><?php echo clean($order['full_name']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo clean($order['email']); ?></td>
                </tr>
                <tr>
                    <th>Username:</th>
                    <td><?php echo clean($order['username']); ?></td>
                </tr>
                <tr>
                    <th>Shipping Address:</th>
                    <td><?php echo nl2br(clean($order['shipping_address'])); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <h6 class="mt-4 mb-3">Order Items</h6>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($item['image']): ?>
                                    <img src="data:<?php echo $item['image_type']; ?>;base64,<?php echo base64_encode($item['image']); ?>" 
                                         alt="<?php echo clean($item['product_name']); ?>"
                                         class="img-thumbnail me-2"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                                <div>
                                    <?php echo clean($item['product_name']); ?>
                                </div>
                            </div>
                        </td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="table-light">
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error in get_order_details.php: " . $e->getMessage());
    echo 'An error occurred while fetching order details';
}
?>
