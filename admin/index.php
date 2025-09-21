<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

// Get dashboard statistics
$stats = [];

// Products Statistics
$stats['products'] = [
    'total' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'out_of_stock' => $pdo->query("SELECT COUNT(*) FROM products WHERE stock = 0")->fetchColumn(),
    'low_stock' => $pdo->query("SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 10")->fetchColumn()
];

// Orders Statistics
try {
    $stats['orders'] = [
        'total' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
        'today' => $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'revenue_today' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
        'revenue_total' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders")->fetchColumn()
    ];

    // Add status counts if status column exists
    $columns = $pdo->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN);
    $status_column = array_filter($columns, function($col) {
        return strtolower($col) === 'status' || strtolower($col) === 'order_status';
    });

    if (!empty($status_column)) {
        $status_column_name = reset($status_column);
        $stats['orders']['pending'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE {$status_column_name} = 'pending'")->fetchColumn();
        $stats['orders']['processing'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE {$status_column_name} = 'processing'")->fetchColumn();
    } else {
        $stats['orders']['pending'] = 0;
        $stats['orders']['processing'] = 0;
    }
} catch (PDOException $e) {
    error_log("Error in orders statistics: " . $e->getMessage());
    $stats['orders'] = [
        'total' => 0,
        'pending' => 0,
        'processing' => 0,
        'today' => 0,
        'revenue_today' => 0,
        'revenue_total' => 0
    ];
}

// Users Statistics
$stats['users'] = [
    'total' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 0")->fetchColumn(),
    'new_today' => $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE() AND is_admin = 0")->fetchColumn()
];

// Categories Statistics
$stats['categories'] = [
    'total' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn()
];

// Get recent orders
try {
    $stmt = $pdo->query("
        SELECT o.*, u.username, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC 
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching recent orders: " . $e->getMessage());
    $recent_orders = [];
}

// Get low stock products
try {
    $stmt = $pdo->query("
        SELECT * FROM products 
        WHERE stock <= 10 AND stock > 0 
        ORDER BY stock ASC 
        LIMIT 5
    ");
    $low_stock_products = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching low stock products: " . $e->getMessage());
    $low_stock_products = [];
}

// Get new users
try {
    $stmt = $pdo->query("
        SELECT * FROM users 
        WHERE is_admin = 0 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $new_users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching new users: " . $e->getMessage());
    $new_users = [];
}
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
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
</head>
<body class="admin-dashboard">
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Dashboard</h1>
            <div>
                <a href="../index.php" class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt">View Site</i> 
                </a>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="row g-4 mb-4">
            <!-- Orders Card -->
            <div class="col-md-3">
                <div class="card bg-primary text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-3">Orders</h6>
                                <h2 class="mb-3"><?php echo $stats['orders']['total']; ?></h2>
                                <div class="mb-2">
                                    <small>
                                        Today: <?php echo $stats['orders']['today']; ?> orders<br>
                                        Pending: <?php echo $stats['orders']['pending']; ?><br>
                                        Processing: <?php echo $stats['orders']['processing']; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                        <a href="orders.php" class="text-white text-decoration-none">View Orders →</a>
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="col-md-3">
                <div class="card bg-success text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-3">Products</h6>
                                <h2 class="mb-3"><?php echo $stats['products']['total']; ?></h2>
                                <div class="mb-2">
                                    <small>
                                        Out of Stock: <?php echo $stats['products']['out_of_stock']; ?><br>
                                        Low Stock: <?php echo $stats['products']['low_stock']; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                        <a href="products.php" class="text-white text-decoration-none">Manage Products →</a>
                    </div>
                </div>
            </div>

            <!-- Users Card -->
            <div class="col-md-3">
                <div class="card bg-info text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-3">Users</h6>
                                <h2 class="mb-3"><?php echo $stats['users']['total']; ?></h2>
                                <div class="mb-2">
                                    <small>
                                        New Today: <?php echo $stats['users']['new_today']; ?><br>
                                        Active Users
                                    </small>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <a href="users.php" class="text-white text-decoration-none">Manage Users →</a>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="col-md-3">
                <div class="card bg-warning text-white stat-card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title mb-3">Revenue</h6>
                                <h2 class="mb-3">$<?php echo number_format($stats['orders']['revenue_total'], 2); ?></h2>
                                <div class="mb-2">
                                    <small>
                                        Today: $<?php echo number_format($stats['orders']['revenue_today'], 2); ?><br>
                                        Total Revenue
                                    </small>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                        <a href="orders.php" class="text-white text-decoration-none">View Details →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="row g-4">
            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td>
                                                <div>
                                                    <?php echo clean($order['username']); ?>
                                                    <div class="text-muted small"><?php echo clean($order['email']); ?></div>
                                                </div>
                                            </td>
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
                                                $status = isset($order['status']) ? $order['status'] : 
                                                         (isset($order['order_status']) ? $order['order_status'] : 'pending');
                                                $class = $status_class[$status] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?php echo $class; ?>">
                                                    <?php echo ucfirst($status); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Low Stock Products</h5>
                        <a href="products.php" class="btn btn-primary btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock_products as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($product['image']): ?>
                                                        <img src="data:<?php echo $product['image_type']; ?>;base64,<?php echo base64_encode($product['image']); ?>" 
                                                             alt="<?php echo clean($product['name']); ?>"
                                                             class="img-thumbnail me-2"
                                                             style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <?php echo clean($product['name']); ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">
                                                    <?php echo $product['stock']; ?> left
                                                </span>
                                            </td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td>
                                                <a href="products.php?edit=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
