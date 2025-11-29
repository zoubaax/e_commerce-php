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
            --gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        /* Hero Slider Styles */
        .hero-slider {
            position: relative;
            overflow: hidden;
            height: 85vh;
            min-height: 650px;
            margin-top: 76px;
            border-radius: 0 0 40px 40px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
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
            transition: opacity 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            max-width: 700px;
            padding: 4rem;
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            margin-left: 10%;
            border: 1px solid rgba(255,255,255,0.1);
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(78,84,200,0.4) 0%, rgba(0,0,0,0.6) 100%);
            z-index: 1;
        }
        
        .slider-controls {
            position: absolute;
            bottom: 40px;
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
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            cursor: pointer;
            transition: all 0.4s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .slider-dot::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: white;
            transition: left 5s linear;
        }
        
        .slider-dot.active {
            background: rgba(255, 255, 255, 0.8);
            transform: scale(1.4);
            border-color: var(--primary-color);
        }
        
        .slider-dot.active::before {
            left: 0;
        }
        
        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            z-index: 10;
        }
        
        .slider-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .slider-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.15) translateY(-2px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
        }
        
        .btn-hero {
            padding: 16px 40px;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.4s ease;
            border: none;
            font-size: 1.1rem;
            margin: 12px 15px 12px 0;
            position: relative;
            overflow: hidden;
        }
        
        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.7s ease;
        }
        
        .btn-hero:hover::before {
            left: 100%;
        }
        
        .btn-primary-custom {
            background: var(--gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(78,84,200,0.3);
        }
        
        .btn-outline-light-custom {
            border: 2px solid white;
            color: white;
            background: transparent;
            backdrop-filter: blur(10px);
        }
        
        .btn-hero:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        
        /* Benefits Section */
        .benefits-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
        }
        
        .benefits-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%234e54c8" opacity="0.03"><polygon points="0,0 1000,50 1000,100 0,100"/></svg>');
            background-size: cover;
        }
        
        .benefit-card {
            background: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
        }
        
        .benefit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .benefit-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        .benefit-card:hover::before {
            transform: scaleX(1);
        }
        
        .icon-lg {
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 2rem;
            border-radius: 50%;
            background: var(--gradient);
            color: white;
            box-shadow: 0 10px 25px rgba(78,84,200,0.3);
            transition: all 0.4s ease;
        }
        
        .benefit-card:hover .icon-lg {
            transform: rotate(15deg) scale(1.1);
        }
        
        /* Featured Products */
        .featured-section {
            padding: 120px 0;
            background: white;
            position: relative;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 4rem;
            text-align: center;
        }
        
        .section-title h2 {
            font-weight: 800;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 5px;
            background: var(--gradient);
            border-radius: 3px;
        }
        
        .product-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            height: 100%;
            background: white;
            position: relative;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient);
            opacity: 0;
            transition: opacity 0.4s ease;
            z-index: 1;
        }
        
        .product-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }
        
        .product-card:hover::before {
            opacity: 0.05;
        }
        
        .product-image {
            height: 280px;
            overflow: hidden;
            position: relative;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.15);
        }
        
        .product-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--accent-color);
            color: white;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            z-index: 2;
            box-shadow: 0 5px 15px rgba(255,107,107,0.3);
        }
        
        .wishlist-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            color: #666;
            z-index: 2;
        }
        
        .wishlist-btn:hover {
            background: var(--accent-color);
            color: white;
            transform: scale(1.1);
        }
        
        .product-info {
            padding: 2rem;
            position: relative;
            z-index: 2;
        }
        
        .product-title {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }
        
        .product-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .original-price {
            font-size: 1.1rem;
            color: #999;
            text-decoration: line-through;
        }
        
        .product-actions {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
        }
        
        .btn-details {
            flex: 1;
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .btn-details:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-cart {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: var(--gradient);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(78,84,200,0.3);
        }
        
        .btn-cart:hover {
            transform: translateY(-2px) scale(1.1);
            box-shadow: 0 8px 20px rgba(78,84,200,0.4);
        }
        
        /* Newsletter Section */
        .newsletter-section {
            padding: 120px 0;
            background: var(--gradient);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .newsletter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff" opacity="0.05"><circle cx="100" cy="50" r="30"/><circle cx="400" cy="20" r="20"/><circle cx="700" cy="70" r="25"/><circle cx="900" cy="40" r="15"/></svg>');
            background-size: cover;
        }
        
        .newsletter-content {
            position: relative;
            z-index: 2;
        }
        
        .newsletter-form {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-control-newsletter {
            height: 65px;
            border-radius: 50px 0 0 50px;
            border: none;
            padding: 0 30px;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .btn-newsletter {
            height: 65px;
            border-radius: 0 50px 50px 0;
            border: none;
            background: var(--dark-color);
            color: white;
            font-weight: 700;
            padding: 0 35px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .btn-newsletter:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        
        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding-top: 100px;
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient);
        }
        
        .footer h5 {
            position: relative;
            margin-bottom: 2rem;
            padding-bottom: 15px;
            font-weight: 700;
        }
        
        .footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }
        
        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 12px;
            padding: 5px 0;
            border-bottom: 1px solid transparent;
        }
        
        .footer-links a:hover {
            color: white;
            transform: translateX(8px);
            border-bottom: 1px solid var(--primary-color);
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: white;
            margin-right: 12px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: var(--primary-color);
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .payment-methods img {
            height: 35px;
            margin-right: 12px;
            margin-bottom: 12px;
            transition: transform 0.3s ease;
        }
        
        .payment-methods img:hover {
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 30px 0;
            margin-top: 70px;
        }
        
        /* View All Products Button */
        .btn-view-all {
            background: var(--gradient);
            color: white;
            padding: 15px 50px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(78,84,200,0.3);
            border: none;
        }
        
        .btn-view-all:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 40px rgba(78,84,200,0.4);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 1200px) {
            .hero-slider {
                height: 75vh;
                min-height: 550px;
            }
            
            .slide-content {
                margin-left: 5%;
                padding: 3rem;
            }
        }
        
        @media (max-width: 992px) {
            .hero-slider {
                height: 65vh;
                min-height: 500px;
            }
            
            .slide-content {
                margin: 0 auto;
                text-align: center;
                padding: 2.5rem;
            }
            
            .slider-btn {
                width: 55px;
                height: 55px;
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-slider {
                height: 55vh;
                min-height: 450px;
                border-radius: 0 0 30px 30px;
            }
            
            .slide-content {
                padding: 2rem;
            }
            
            .slide-content h1 {
                font-size: 2.2rem;
            }
            
            .btn-hero {
                padding: 14px 30px;
                margin: 8px 10px 8px 0;
            }
            
            .benefit-card, .product-card {
                margin-bottom: 30px;
            }
            
            .slider-nav {
                padding: 0 20px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-slider {
                height: 50vh;
                min-height: 400px;
            }
            
            .slide-content {
                padding: 1.5rem;
            }
            
            .slide-content h1 {
                font-size: 1.8rem;
            }
            
            .btn-hero {
                display: block;
                width: 100%;
                margin: 8px 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Slider Section -->
    <section class="hero-slider">
        <div class="slider-container">
            <!-- Slide 1 -->
            <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
                <div class="slide-content">
                    <h1 class="display-3 fw-bold mb-4">Summer Collection 2024</h1>
                    <p class="lead mb-5 fs-5">Discover our latest summer fashion with up to 50% off on selected items. Limited time offer!</p>
                    <div class="d-flex flex-wrap">
                        <a href="products.php" class="btn btn-hero btn-primary-custom">Shop Now</a>
                        <a href="#featured" class="btn btn-hero btn-outline-light-custom">View Collection</a>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
                <div class="slide-content">
                    <h1 class="display-3 fw-bold mb-4">Tech Gadgets Sale</h1>
                    <p class="lead mb-5 fs-5">Upgrade your tech with our premium electronics at unbeatable prices. Free shipping on all orders!</p>
                    <div class="d-flex flex-wrap">
                        <a href="products.php?category=electronics" class="btn btn-hero btn-primary-custom">Shop Electronics</a>
                        <a href="products.php" class="btn btn-hero btn-outline-light-custom">All Products</a>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1601924582970-9238bcb495d9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1476&q=80');">
                <div class="slide-content">
                    <h1 class="display-3 fw-bold mb-4">Home & Lifestyle</h1>
                    <p class="lead mb-5 fs-5">Transform your living space with our curated home collection. Quality products for everyday life.</p>
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
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="icon-lg">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4 class="h4 fw-bold mb-3">Free Shipping</h4>
                        <p class="mb-0 text-muted fs-6">On all orders over $50. Fast and reliable delivery to your doorstep with real-time tracking.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="icon-lg">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h4 class="h4 fw-bold mb-3">Easy Returns</h4>
                        <p class="mb-0 text-muted fs-6">30-day hassle-free return policy. Your satisfaction is 100% guaranteed with no questions asked.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="icon-lg">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="h4 fw-bold mb-3">Secure Payment</h4>
                        <p class="mb-0 text-muted fs-6">100% secure checkout with multiple payment options available. Your data is always protected.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="featured-section">
        <div class="container">
            <div class="section-title">
                <h2 class="display-4 fw-bold">Featured Products</h2>
                <p class="text-muted fs-5">Handpicked selection of our best-selling items</p>
            </div>
            <div class="row g-5">
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 LIMIT 8");
                while($product = $stmt->fetch()): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo !empty($product['image']) ? 'get_image.php?id=' . $product['id'] : 'assets/images/placeholder.jpg'; ?>" 
                                     alt="<?php echo clean($product['name']); ?>">
                                <?php if($product['discount'] > 0): ?>
                                    <div class="product-badge">-<?php echo $product['discount']; ?>% OFF</div>
                                <?php endif; ?>
                                <button class="wishlist-btn">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <h5 class="product-title"><?php echo clean($product['name']); ?></h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                    <?php if(isset($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                        <div class="original-price">$<?php echo number_format($product['original_price'], 2); ?></div>
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
            <div class="text-center mt-5 pt-3">
                <a href="products.php" class="btn btn-view-all">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center newsletter-content">
                    <h2 class="display-5 fw-bold mb-4">Stay Updated</h2>
                    <p class="mb-5 fs-5">Get the latest updates on new products, exclusive offers, and upcoming sales delivered straight to your inbox.</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-newsletter" placeholder="Enter your email address" required>
                            <button type="submit" class="btn btn-newsletter">Subscribe Now</button>
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
                    <p class="text-muted fs-6">Your trusted online shopping destination for quality products at affordable prices. We're committed to providing the best shopping experience with exceptional customer service.</p>
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
                    <h6 class="mt-4 mb-3 fw-bold">Payment Methods</h6>
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
    
    <!-- Enhanced Slider Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const dots = document.querySelectorAll('.slider-dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            let currentSlide = 0;
            let slideInterval;
            let isAnimating = false;
            
            // Function to show a specific slide
            function showSlide(index) {
                if (isAnimating) return;
                isAnimating = true;
                
                // Hide all slides
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => {
                    dot.classList.remove('active');
                    dot.style.setProperty('--progress', '0%');
                });
                
                // Show the selected slide
                setTimeout(() => {
                    slides[index].classList.add('active');
                    dots[index].classList.add('active');
                    currentSlide = index;
                    isAnimating = false;
                    
                    // Reset progress animation
                    if (dots[index].classList.contains('active')) {
                        dots[index].style.setProperty('--progress', '100%');
                    }
                }, 100);
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
                slideInterval = setInterval(nextSlide, 6000);
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
            
            // Add scroll animations
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
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>