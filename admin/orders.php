<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
require_once 'auth.php';
require_admin();

// Initialize variables
$error = '';
$success = '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total number of orders
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_pages = ceil($total_orders / $limit);

// Get orders with user information
$stmt = $pdo->prepare("
    SELECT o.*, u.username, u.email 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

// Get order statistics
$stats = [
    'total' => $total_orders,
    'today' => $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'revenue_today' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'revenue_total' => $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders")->fetchColumn()
];

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid request';
    } else {
        $order_id = (int)$_POST['order_id'];
        $new_status = clean($_POST['status']);
        
        $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (in_array($new_status, $valid_statuses)) {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $order_id])) {
                $success = 'Order status updated successfully';
            } else {
                $error = 'Failed to update order status';
            }
        }
    }
}

// Get filters
$status_filter = isset($_GET['status']) ? clean($_GET['status']) : '';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? clean($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? clean($_GET['date_to']) : '';

// Pagination
$per_page = 15;
$offset = ($page - 1) * $per_page;

// Build query
$query = "
    SELECT o.*, 
           u.username, u.email, u.full_name,
           COUNT(oi.id) as total_items,
           SUM(oi.quantity) as total_quantity
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE 1=1
";
$params = [];

if ($status_filter) {
    $query .= " AND o.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $query .= " AND (u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ? OR o.id LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
}

if ($date_from) {
    $query .= " AND DATE(o.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $query .= " AND DATE(o.created_at) <= ?";
    $params[] = $date_to;
}

// Add GROUP BY and ORDER BY
$query .= " GROUP BY o.id ORDER BY o.created_at DESC";

// Add LIMIT and OFFSET directly to the query
$query .= " LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;

// Get total count for pagination
$count_query = "
    SELECT COUNT(DISTINCT o.id) 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE 1=1
";
if ($status_filter) {
    $count_query .= " AND o.status = ?";
}
if ($search) {
    $count_query .= " AND (u.username LIKE ? OR u.email LIKE ? OR u.full_name LIKE ? OR o.id LIKE ?)";
}
if ($date_from) {
    $count_query .= " AND DATE(o.created_at) >= ?";
}
if ($date_to) {
    $count_query .= " AND DATE(o.created_at) <= ?";
}

$stmt = $pdo->prepare($count_query);
$count_params = array_slice($params, 0, -2); // Remove LIMIT and OFFSET params
$stmt->execute($count_params);
$total_orders = $stmt->fetchColumn();
$total_pages = ceil($total_orders / $per_page);

// Get orders
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Get order statistics
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$order_stats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
    <style>
        .stat-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="admin-dashboard">
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Orders Management</h1>
            <div class="d-flex gap-2">
                <a href="../index.php" class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt">View Site</i> 
                </a>
            </div>
        </div>

        

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Order Statistics -->
        <div class="row mb-4">
            <?php
            $status_colors = [
                'pending' => 'warning',
                'processing' => 'info',
                'shipped' => 'primary',
                'delivered' => 'success',
                'cancelled' => 'danger'
            ];
            foreach ($status_colors as $status => $color):
                $count = $order_stats[$status] ?? 0;
            ?>
                <div class="col-md-2 mb-3">
                    <div class="card bg-<?php echo $color; ?> text-white">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo ucfirst($status); ?></h6>
                            <p class="card-text h3"><?php echo $count; ?></p>
                            <a href="?status=<?php echo $status; ?>" class="text-white text-decoration-none">View →</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-md-2 mb-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <h6 class="card-title">Total Orders</h6>
                        <p class="card-text h3"><?php echo $total_orders; ?></p>
                        <a href="?" class="text-white text-decoration-none">View All →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="<?php echo $search; ?>" 
                               placeholder="Order ID, customer name, or email">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All Statuses</option>
                            <?php foreach ($status_colors as $status => $color): ?>
                                <option value="<?php echo $status; ?>" <?php echo $status_filter === $status ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($status); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_from" value="<?php echo $date_from; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_to" value="<?php echo $date_to; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="?" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td>
                                        <div>
                                            <strong><?php echo clean($order['full_name']); ?></strong>
                                            <div class="text-muted small">
                                                <?php echo clean($order['email']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <?php echo $order['total_items']; ?> items
                                        <div class="text-muted small">
                                            (<?php echo $order['total_quantity']; ?> units)
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <form action="orders.php" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm status-select" 
                                                    data-order-id="<?php echo $order['id']; ?>"
                                                    onchange="this.form.submit()">
                                                <?php foreach ($status_colors as $status => $_): ?>
                                                    <option value="<?php echo $status; ?>" 
                                                            <?php echo $status === $order['status'] ? 'selected' : ''; ?>>
                                                        <?php echo ucfirst($status); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary view-order-details" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#orderModal"
                                                data-order-id="<?php echo $order['id']; ?>">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo ($page - 1); ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo ($page + 1); ?>&status=<?php echo $status_filter; ?>&search=<?php echo $search; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="orderDetails">
                        Loading...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderButtons = document.querySelectorAll('.view-order-details');
        
        orderButtons.forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                const detailsContainer = document.getElementById('orderDetails');
                
                // Show loading state
                detailsContainer.innerHTML = 'Loading...';
                
                // Fetch order details
                fetch('get_order_details.php?id=' + orderId)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.text();
                    })
                    .then(html => {
                        if (html.trim() === '') {
                            detailsContainer.innerHTML = 'No order details found.';
                        } else {
                            detailsContainer.innerHTML = html;
                        }
                    })
                    .catch(error => {
                        detailsContainer.innerHTML = 'Error loading order details. Please try again.';
                        console.error('Error:', error);
                    });
            });
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
</body>
</html>
