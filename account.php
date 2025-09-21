<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to access your account';
    redirect('login.php');
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch user's orders
$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.id) as total_items,
           (SELECT image FROM products WHERE id = oi2.product_id LIMIT 1) as first_product_image
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN order_items oi2 ON o.id = oi2.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-user-circle fa-4x text-primary"></i>
                            <h5 class="mt-2"><?php echo clean($user['full_name']); ?></h5>
                            <p class="text-muted"><?php echo clean($user['email']); ?></p>
                        </div>
                        <div class="list-group">
                            <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                            <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="fas fa-shopping-bag me-2"></i> Orders
                            </a>
                            <a href="#settings" class="list-group-item list-group-item-action" data-bs-toggle="list">
                                <i class="fas fa-cog me-2"></i> Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card">
                            <div class="card-header">
                                <h4>My Profile</h4>
                            </div>
                            <div class="card-body">
                                <form action="update_profile.php" method="POST">
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
                                               value="<?php echo clean($user['phone']); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo clean($user['address']); ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="orders">
                        <div class="card">
                            <div class="card-header">
                                <h4>My Orders</h4>
                            </div>
                            <div class="card-body">
                                <?php if (empty($orders)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                        <p class="lead">You haven't placed any orders yet.</p>
                                        <a href="products.php" class="btn btn-primary">Start Shopping</a>
                                    </div>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($orders as $order): ?>
                                            <a href="get_order_details.php?id=<?php echo $order['id']; ?>" class="list-group-item list-group-item-action">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">Order #<?php echo $order['id']; ?></h6>
                                                        <p class="mb-1 text-muted">
                                                            <?php echo date('F j, Y', strtotime($order['created_at'])); ?> •
                                                            <?php echo $order['total_items']; ?> items •
                                                            $<?php echo number_format($order['total_amount'], 2); ?>
                                                        </p>
                                                        <span class="badge bg-<?php echo $order['status'] === 'delivered' ? 'success' : 'primary'; ?>">
                                                            <?php echo ucfirst($order['status']); ?>
                                                        </span>
                                                    </div>
                                                    <?php if (!empty($order['first_product_image'])): ?>
                                                        <img src="<?php echo clean($order['first_product_image']); ?>" 
                                                             alt="Order Preview" 
                                                             class="rounded" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php endif; ?>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="settings">
                        <div class="card">
                            <div class="card-header">
                                <h4>Account Settings</h4>
                            </div>
                            <div class="card-body">
                                <form action="update_password.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        // Function to toggle password visibility
        function togglePasswordVisibility(buttonId, inputId) {
            document.getElementById(buttonId).addEventListener('click', function() {
                const passwordInput = document.getElementById(inputId);
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }

        // Initialize password toggles for all password fields
        togglePasswordVisibility('toggleCurrentPassword', 'current_password');
        togglePasswordVisibility('toggleNewPassword', 'new_password');
        togglePasswordVisibility('toggleConfirmPassword', 'confirm_password');
    </script>
</body>
</html>
