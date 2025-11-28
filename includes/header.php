<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Shop - Premium Online Shopping</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
    <style>
        :root {
            --primary-color: #4e54c8;
            --secondary-color: #8f94fb;
            --accent-color: #ff6b6b;
            --dark-color: #2c3e50;
            --light-color: #f8f9fa;
        }
        
        /* Enhanced Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--dark-color) 0%, #34495e 100%) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 0.8rem 0;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(45deg, #fff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-link {
            position: relative;
            font-weight: 500;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-link:hover::after {
            width: 80%;
        }
        
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
        
        /* Hero Slider Styles */
        .hero-slider {
            position: relative;
            overflow: hidden;
            height: 80vh;
            min-height: 600px;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .slider-container {
            height: 100%;
            position: relative;
        }
        
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
        }
        
        .slide.active {
            opacity: 1;
        }
        
        .slide-content {
            position: relative;
            z-index: 2;
            color: white;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
            max-width: 650px;
            padding: 3rem;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-left: 10%;
        }
        
        .slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(78,84,200,0.3) 0%, rgba(0,0,0,0.5) 100%);
            z-index: 1;
        }
        
        .slider-controls {
            position: absolute;
            bottom: 30px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 10;
        }
        
        .slider-dots {
            display: flex;
            gap: 15px;
        }
        
        .slider-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .slider-dot.active {
            background: white;
            transform: scale(1.3);
            border-color: var(--primary-color);
        }
        
        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 10;
        }
        
        .slider-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .slider-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .btn-hero {
            padding: 14px 35px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: none;
            font-size: 1.1rem;
            margin: 10px 10px 10px 0;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .btn-outline-light-custom {
            border: 2px solid white;
            color: white;
            background: transparent;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Benefits Section */
        .benefits-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .benefit-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .icon-lg {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        /* Featured Products */
        .featured-section {
            padding: 100px 0;
            background: white;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }
        
        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .wishlist-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            color: #555;
        }
        
        .wishlist-btn:hover {
            background: var(--accent-color);
            color: white;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-details {
            flex: 1;
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 8px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-details:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-cart {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .btn-cart:hover {
            background: var(--secondary-color);
        }
        
        /* Newsletter Section */
        .newsletter-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
        
        .newsletter-form {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-control-newsletter {
            height: 60px;
            border-radius: 50px 0 0 50px;
            border: none;
            padding: 0 25px;
            font-size: 1.1rem;
        }
        
        .btn-newsletter {
            height: 60px;
            border-radius: 0 50px 50px 0;
            border: none;
            background: var(--dark-color);
            color: white;
            font-weight: 600;
            padding: 0 30px;
            transition: all 0.3s ease;
        }
        
        .btn-newsletter:hover {
            background: #1a252f;
        }
        
        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding-top: 80px;
        }
        
        .footer h5 {
            position: relative;
            margin-bottom: 1.5rem;
            padding-bottom: 10px;
        }
        
        .footer h5:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--primary-color);
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 10px;
        }
        
        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: white;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }
        
        .payment-methods img {
            height: 30px;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 20px 0;
            margin-top: 50px;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .hero-slider {
                height: 60vh;
                min-height: 500px;
            }
            
            .slide-content {
                margin-left: 5%;
                padding: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-slider {
                height: 50vh;
                min-height: 400px;
            }
            
            .slider-btn {
                width: 45px;
                height: 45px;
                font-size: 1.2rem;
            }
            
            .slide-content {
                text-align: center;
                padding: 1.5rem;
                margin: 0 auto;
            }
            
            .slide-content h1 {
                font-size: 2rem;
            }
            
            .benefit-card, .product-card {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
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
                        <a class="nav-link px-3 active" href="index.php">
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
                    <li class="nav-item">
                        <a class="nav-link px-3" href="about.php">
                            <i class="fas fa-info-circle me-1"></i> About
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
                    
                    <!-- Search -->
                    <li class="nav-item d-flex align-items-center px-2">
                        <div class="input-group search-box">
                            <input type="text" class="form-control" placeholder="Search products...">
                            <button class="btn btn-outline-light" type="button">
                                <i class="fas fa-search"></i>
                            </button>
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
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                                <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
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

    <!-- Hero Slider Section -->
    <section class="hero-slider mt-5">
        <div class="slider-container">
            <!-- Slide 1 -->
            <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
                <div class="slide-content">
                    <h1 class="display-4 fw-bold mb-4">Summer Collection 2024</h1>
                    <p class="lead mb-5">Discover our latest summer fashion with up to 50% off on selected items. Limited time offer!</p>
                    <div class="d-flex flex-wrap">
                        <a href="products.php" class="btn btn-hero btn-primary-custom">Shop Now</a>
                        <a href="#featured" class="btn btn-hero btn-outline-light-custom">View Collection</a>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
                <div class="slide-content">
                    <h1 class="display-4 fw-bold mb-4">Tech Gadgets Sale</h1>
                    <p class="lead mb-5">Upgrade your tech with our premium electronics at unbeatable prices. Free shipping on all orders!</p>
                    <div class="d-flex flex-wrap">
                        <a href="products.php?category=electronics" class="btn btn-hero btn-primary-custom">Shop Electronics</a>
                        <a href="products.php" class="btn btn-hero btn-outline-light-custom">All Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1601924582970-9238bcb495d9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1476&q=80');">
                <div class="slide-content">
                    <h1 class="display-4 fw-bold mb-4">Home & Lifestyle</h1>
                    <p class="lead mb-5">Transform your living space with our curated home collection. Quality products for everyday life.</p>
                    <div class="d-flex flex-wrap">
                        <a href="products.php?category=home" class="btn btn-hero btn-primary-custom">Shop Home</a>
                        <a href="products.php" class="btn btn-hero btn-outline-light-custom">All Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Slider Navigation -->
            <div class="slider-nav">
                <button class="slider-btn prev-btn">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="slider-btn next-btn">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <!-- Slider Dots -->
            <div class="slider-controls">
                <div class="slider-dots">
                    <div class="slider-dot active" data-slide="0"></div>
                    <div class="slider-dot" data-slide="1"></div>
                    <div class="slider-dot" data-slide="2"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="icon-lg">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4 class="h5">Free Shipping</h4>
                        <p class="mb-0 text-muted">On all orders over $50. Fast and reliable delivery to your doorstep.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="icon-lg">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h4 class="h5">Easy Returns</h4>
                        <p class="mb-0 text-muted">30-day hassle-free return policy. Your satisfaction is guaranteed.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="icon-lg">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="h5">Secure Payment</h4>
                        <p class="mb-0 text-muted">100% secure checkout with multiple payment options available.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="featured-section">
        <div class="container">
            <div class="section-title">
                <h2 class="display-5 fw-bold">Featured Products</h2>
                <p class="text-muted">Handpicked selection of our best items</p>
            </div>
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 LIMIT 8");
                while($product = $stmt->fetch()): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo !empty($product['image']) ? 'get_image.php?id=' . $product['id'] : 'assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo clean($product['name']); ?>">
                                <?php if($product['discount'] > 0): ?>
                                    <div class="product-badge">-<?php echo $product['discount']; ?>%</div>
                                <?php endif; ?>
                                <button class="wishlist-btn">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <h5 class="product-title"><?php echo clean($product['name']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                    <?php if($product['original_price'] > $product['price']): ?>
                                        <div class="text-muted text-decoration-line-through">$<?php echo number_format($product['original_price'], 2); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-actions">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-details">View Details</a>
                                    <button class="btn-cart">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-5">
                <a href="products.php" class="btn btn-primary btn-lg px-5 py-3">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="h1 mb-3">Subscribe to Our Newsletter</h2>
                    <p class="mb-5">Get the latest updates on new products, exclusive offers, and upcoming sales delivered straight to your inbox.</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-newsletter" placeholder="Your email address" required>
                            <button type="submit" class="btn btn-newsletter">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
                    <h5>E-Shop</h5>
                    <p class="text-muted">Your trusted online shopping destination for quality products at affordable prices. We're committed to providing the best shopping experience.</p>
                    <div class="social-icons mt-4">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5>Shop</h5>
                    <div class="footer-links">
                        <a href="products.php">All Products</a>
                        <a href="products.php?category=men">Men's Fashion</a>
                        <a href="products.php?category=women">Women's Fashion</a>
                        <a href="products.php?category=electronics">Electronics</a>
                        <a href="products.php?category=accessories">Accessories</a>
                        <a href="products.php?category=home">Home & Garden</a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h5>Help</h5>
                    <div class="footer-links">
                        <a href="faq.php">FAQs</a>
                        <a href="shipping.php">Shipping Info</a>
                        <a href="returns.php">Returns</a>
                        <a href="size-guide.php">Size Guide</a>
                        <a href="contact.php">Contact Us</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> 123 Shop Street, City, Country 12345</li>
                        <li class="mb-3"><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                        <li class="mb-3"><i class="fas fa-envelope me-2"></i> info@eshop.com</li>
                        <li class="mb-3"><i class="fas fa-clock me-2"></i> Mon-Fri: 9AM-6PM, Sat: 10AM-4PM</li>
                    </ul>
                    <h6 class="mt-4 mb-3">Payment Methods</h6>
                    <div class="payment-methods">
                        <img src="assets/images/payment/visa.svg" alt="Visa">
                        <img src="assets/images/payment/mastercard.svg" alt="Mastercard">
                        <img src="assets/images/payment/paypal.svg" alt="PayPal">
                        <img src="assets/images/payment/apple-pay.svg" alt="Apple Pay">
                        <img src="assets/images/payment/google-pay.svg" alt="Google Pay">
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0 text-muted">&copy; 2024 E-Shop. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="privacy.php" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                        <a href="terms.php" class="text-muted text-decoration-none">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Slider Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slider-dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            let currentSlide = 0;
            let slideInterval;
            
            // Function to show a specific slide
            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                
                // Show the selected slide
                slides[index].classList.add('active');
                dots[index].classList.add('active');
                currentSlide = index;
            }
            
            // Function to show next slide
            function nextSlide() {
                let nextIndex = (currentSlide + 1) % slides.length;
                showSlide(nextIndex);
            }
            
            // Function to show previous slide
            function prevSlide() {
                let prevIndex = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prevIndex);
            }
            
            // Start automatic sliding
            function startSlideShow() {
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            // Stop automatic sliding
            function stopSlideShow() {
                clearInterval(slideInterval);
            }
            
            // Event listeners for navigation buttons
            nextBtn.addEventListener('click', function() {
                stopSlideShow();
                nextSlide();
                startSlideShow();
            });
            
            prevBtn.addEventListener('click', function() {
                stopSlideShow();
                prevSlide();
                startSlideShow();
            });
            
            // Event listeners for dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    stopSlideShow();
                    showSlide(index);
                    startSlideShow();
                });
            });
            
            // Pause slideshow when hovering over slider
            const slider = document.querySelector('.hero-slider');
            slider.addEventListener('mouseenter', stopSlideShow);
            slider.addEventListener('mouseleave', startSlideShow);
            
            // Initialize the slider
            startSlideShow();
            
            // Add animation to elements when they come into view
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe elements for animation
            document.querySelectorAll('.benefit-card, .product-card').forEach(el => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>