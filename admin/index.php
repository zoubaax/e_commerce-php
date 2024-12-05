<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

// Get counts for dashboard
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <div class="theme-toggle-wrapper">
                            <label class="theme-switch">
                                <input type="checkbox" id="theme-toggle">
                                <span class="slider round">
                                    <i class="fas fa-sun"></i>
                                    <i class="fas fa-moon"></i>
                                </span>
                            </label>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php?logout=true">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="mb-4">Dashboard</h1>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-5">
            <div class="col">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text display-6"><?php echo $productCount; ?></p>
                        <a href="products.php" class="btn btn-light">Manage Products</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <p class="card-text display-6"><?php echo $categoryCount; ?></p>
                        <a href="categories.php" class="btn btn-light">Manage Categories</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Orders</h5>
                        <p class="card-text display-6"><?php echo $orderCount; ?></p>
                        <a href="orders.php" class="btn btn-light">View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text display-6"><?php echo $userCount; ?></p>
                        <a href="users.php" class="btn btn-light">Manage Users</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <h3>Recent Orders</h3>
                <?php
                $stmt = $pdo->query("SELECT o.*, u.username 
                                   FROM orders o 
                                   JOIN users u ON o.user_id = u.id 
                                   ORDER BY o.created_at DESC 
                                   LIMIT 5");
                $recentOrders = $stmt->fetchAll();
                ?>
                <div class="list-group">
                    <?php foreach ($recentOrders as $order): ?>
                    <a href="orders.php?id=<?php echo $order['id']; ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">Order #<?php echo $order['id']; ?></h5>
                            <small><?php echo date('M d, Y', strtotime($order['created_at'])); ?></small>
                        </div>
                        <p class="mb-1">By <?php echo htmlspecialchars($order['username']); ?></p>
                        <small>Status: <?php echo ucfirst($order['status']); ?></small>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <h3>Low Stock Products</h3>
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
                $lowStockProducts = $stmt->fetchAll();
                ?>
                <div class="list-group">
                    <?php foreach ($lowStockProducts as $product): ?>
                    <a href="products.php?id=<?php echo $product['id']; ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <small class="text-danger"><?php echo $product['stock']; ?> left</small>
                        </div>
                        <p class="mb-1">$<?php echo number_format($product['price'], 2); ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>
