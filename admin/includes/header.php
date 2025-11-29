<?php
// admin/includes/header.php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/php/e commerce/assets/css/style.css">
    <link rel="stylesheet" href="/php/e commerce/assets/css/dark-theme.css">
    <style>
        :root {
            --primary-color: #4e54c8;
            --secondary-color: #8f94fb;
            --accent-color: #ff6b6b;
            --dark-color: #1a1d23;
            --darker-color: #15171c;
        }

        .navbar-admin {
            background: linear-gradient(135deg, var(--darker-color) 0%, var(--dark-color) 100%) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            border-bottom: 1px solid #2d3748;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(45deg, #fff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-link {
            position: relative;
            font-weight: 500;
            margin: 0 3px;
            border-radius: 8px;
            transition: all 0.3s ease;
            padding: 0.6rem 1rem !important;
            color: #e2e8f0 !important;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: rgba(78,84,200,0.2);
            color: var(--primary-color) !important;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 2px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .nav-link.logout {
            color: var(--accent-color) !important;
            font-weight: 600;
        }

        .nav-link.logout:hover {
            background: var(--accent-color);
            color: white !important;
        }

        .theme-toggle-wrapper {
            display: flex;
            align-items: center;
        }

        .theme-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
            cursor: pointer;
        }

        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #343a40;
            border: 1px solid #495057;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            z-index: 2;
        }

        input:checked + .slider {
            background-color: #f8f9fa;
        }

        input:checked + .slider:before {
            transform: translateX(28px);
            background-color: #212529;
        }

        .slider i {
            position: absolute;
            top: 6px;
            font-size: 14px;
            color: white;
            z-index: 1;
        }

        .slider i.fa-sun {
            left: 8px;
        }

        .slider i.fa-moon {
            right: 8px;
            color: #212529;
        }

        input:checked + .slider i.fa-sun {
            color: #ffc107;
        }

        input:checked + .slider i.fa-moon {
            color: white;
        }

        .user-info {
            color: #e2e8f0;
            font-size: 0.9rem;
            margin-right: 1rem;
        }

        @media (max-width: 768px) {
            .nav-link {
                padding: 0.75rem 1rem !important;
                margin: 2px 0;
            }
            
            .user-info {
                margin: 0.5rem 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-cog me-2"></i>E-Shop Admin
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : ''; ?>" href="products.php">
                            <i class="fas fa-box me-1"></i>Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                            <i class="fas fa-tags me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                            <i class="fas fa-shopping-bag me-1"></i>Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : ''; ?>" href="users.php">
                            <i class="fas fa-users me-1"></i>Users
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav align-items-center">
                    <!-- User Info -->
                    <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item">
                        <span class="user-info">
                            <i class="fas fa-user me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                            (Admin)
                        </span>
                    </li>
                    <?php endif; ?>
                    
                    <!-- Theme Toggle -->
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
                    
                    <!-- Logout -->
                    <li class="nav-item">
                        <a class="nav-link logout" href="../logout.php" onclick="return confirm('Are you sure you want to logout?');">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            const currentTheme = localStorage.getItem('admin-theme') || 'light';
            
            if (currentTheme === 'dark') {
                themeToggle.checked = true;
                document.body.classList.add('dark-theme');
            }

            themeToggle.addEventListener('change', function() {
                if (this.checked) {
                    document.body.classList.add('dark-theme');
                    localStorage.setItem('admin-theme', 'dark');
                } else {
                    document.body.classList.remove('dark-theme');
                    localStorage.setItem('admin-theme', 'light');
                }
            });
        });
    </script>
</body>
</html>