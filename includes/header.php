<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <!-- Brand with logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <i class="fas fa-shopping-bag me-2"></i>
            <span class="fw-bold">E-Shop</span>
        </a>
        
        <!-- Mobile toggle button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Main navigation -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link px-3" href="index.php">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="products.php">
                        <i class="fas fa-box-open me-1"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3" href="categories.php">
                        <i class="fas fa-list me-1"></i> Categories
                    </a>
                </li>
            </ul>
            
            <!-- Right-aligned items -->
            <ul class="navbar-nav ms-auto">
                <!-- Theme toggle -->
                <li class="nav-item d-flex align-items-center px-2">
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
                
                <!-- Cart with badge -->
                <li class="nav-item">
                    <a class="nav-link position-relative px-3" href="cart.php">
                        <i class="fas fa-shopping-cart me-1"></i> Cart
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <span class="position-absolute top-10 start-75 translate-middle badge rounded-pill bg-primary">
                                <?php
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $count = $stmt->fetchColumn();
                                echo $count > 9 ? '9+' : $count;
                                ?>
                                <span class="visually-hidden">items in cart</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- User section -->
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Account dropdown for better organization -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle px-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> My Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="account.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <?php if($_SESSION['is_admin']): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-warning" href="admin/"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Login/Register with icons -->
                    <li class="nav-item">
                        <a class="nav-link px-3" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 btn btn-outline-light ms-2" href="register.php">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<style>
    /* Enhanced theme toggle switch */
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
    
    /* Hover effects */
    .nav-link {
        transition: all 0.2s ease;
    }
    
    .nav-link:hover {
        transform: translateY(-2px);
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
</style>