<?php
// includes/header.php - DEBUG VERSION
require_once 'config/config.php';

// Debug session data
echo "<!-- DEBUG SESSION DATA: ";
echo "user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . ", ";
echo "username: " . ($_SESSION['username'] ?? 'NOT SET') . ", ";
echo "is_admin: " . ($_SESSION['is_admin'] ?? 'NOT SET');
echo " -->";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Shop - Premium Online Shopping</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-shopping-bag me-2"></i>
                <span class="fw-bold">E-Shop</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">
                            <i class="fas fa-box-open me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">
                            <i class="fas fa-list me-1"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle me-1"></i> About
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Search -->
                    <li class="nav-item me-3">
                        <div class="input-group search-box">
                            <input type="text" class="form-control" placeholder="Search products...">
                            <button class="btn btn-outline-light" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </li>
                    
                    <!-- Theme toggle -->
                    <li class="nav-item me-3">
                        <div class="theme-toggle-wrapper">
                            <label class="theme-switch m-0">
                                <input type="checkbox" id="theme-toggle">
                                <span class="slider round">
                                    <i class="fas fa-sun"></i>
                                    <i class="fas fa-moon"></i>
                                </span>
                            </label>
                        </div>
                    </li>
                    
                    <!-- Cart -->
                    <li class="nav-item me-3">
                        <a class="nav-link position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i> Cart
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-badge">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $count = $stmt->fetchColumn();
                                        echo $count > 9 ? '9+' : $count;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <!-- User section - SIMPLIFIED VERSION -->
                    <?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                        <!-- DEBUG: User is logged in -->
                        <!-- DEBUG: user_id = <?php echo $_SESSION['user_id']; ?> -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> 
                                <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Account'; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="account.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>Orders</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                                <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="admin/"><i class="fas fa-cog me-2"></i>Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- DEBUG: User is NOT logged in -->
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light" href="register.php">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            padding: 0.8rem 0;
        }
        .nav-link { padding: 0.5rem 1rem !important; }
        .nav-link:hover { background: rgba(255,255,255,0.1); }
        .search-box { max-width: 250px; }
        .search-box .form-control { 
            border-radius: 20px 0 0 20px;
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .search-box .btn { 
            border-radius: 0 20px 20px 0;
            border: 1px solid rgba(255,255,255,0.2);
            border-left: none;
        }
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
            cursor: pointer;
        }
        .theme-switch input { opacity: 0; }
        .slider {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #343a40;
            border-radius: 34px;
            transition: .4s;
        }
        .slider:before {
            content: "";
            position: absolute;
            height: 22px; width: 22px;
            left: 4px; bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: .4s;
        }
        input:checked + .slider { background: #f8f9fa; }
        input:checked + .slider:before { transform: translateX(28px); }
        .cart-badge { font-size: 0.7rem; padding: 0.25em 0.6em; }
    </style>