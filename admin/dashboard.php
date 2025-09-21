<?php
require_once '../config/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['error'] = 'You do not have permission to access this page';
    redirect('../login.php');
}

// Get quick statistics
$stats = [];

// Total Orders
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$stats['total_orders'] = $stmt->fetchColumn();

// Recent Orders (last 24 hours)
$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
$stats['recent_orders'] = $stmt->fetchColumn();

// Total Products
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$stats['total_products'] = $stmt->fetchColumn();

// Total Users
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0");
$stats['total_users'] = $stmt->fetchColumn();

// Get recent orders
$stmt = $pdo->query("
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5
");
$recent_orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid py-4">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <p class="card-text display-6"><?php echo $stats['total_orders']; ?></p>
                        <a href="orders.php" class="text-white">View All Orders →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Recent Orders (24h)</h5>
                        <p class="card-text display-6"><?php echo $stats['recent_orders']; ?></p>
                        <a href="orders.php?period=recent" class="text-white">View Recent Orders →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <p class="card-text display-6"><?php echo $stats['total_products']; ?></p>
                        <a href="products.php" class="text-white">Manage Products →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-6"><?php echo $stats['total_users']; ?></p>
                        <a href="users.php" class="text-white">Manage Users →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="orders.php" class="btn btn-primary btn-sm">View All Orders</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo clean($order['username']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
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
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="orders.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
