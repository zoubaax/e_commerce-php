<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
    <style>
        /* Hero Slider Styles */
        .hero-slider {
            position: relative;
            overflow: hidden;
            height: 70vh;
            min-height: 500px;
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
            max-width: 600px;
            padding: 2rem;
        }
        
        .slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        
        .slider-controls {
            position: absolute;
            bottom: 20px;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            z-index: 10;
        }
        
        .slider-dots {
            display: flex;
            gap: 10px;
        }
        
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .slider-dot.active {
            background: white;
            transform: scale(1.2);
        }
        
        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 10;
        }
        
        .slider-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .slider-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .btn-hero {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        /* Benefits Section */
        .icon-lg {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        /* Product Cards */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-slider {
                height: 60vh;
                min-height: 400px;
            }
            
            .slider-btn {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .slide-content {
                text-align: center;
                padding: 1rem;
            }
            
            .slide-content h1 {
                font-size: 2rem;
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
                <div class="container">
                    <div class="slide-content">
                        <h1 class="display-4 fw-bold mb-4">Summer Collection 2024</h1>
                        <p class="lead mb-5">Discover our latest summer fashion with up to 50% off on selected items.</p>
                        <div class="d-flex gap-3">
                            <a href="products.php" class="btn btn-light btn-hero">Shop Now</a>
                            <a href="#featured" class="btn btn-outline-light btn-hero">View Collection</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
                <div class="container">
                    <div class="slide-content">
                        <h1 class="display-4 fw-bold mb-4">Tech Gadgets Sale</h1>
                        <p class="lead mb-5">Upgrade your tech with our premium electronics at unbeatable prices.</p>
                        <div class="d-flex gap-3">
                            <a href="products.php?category=electronics" class="btn btn-light btn-hero">Shop Electronics</a>
                            <a href="products.php" class="btn btn-outline-light btn-hero">All Products</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1601924582970-9238bcb495d9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1476&q=80');">
                <div class="container">
                    <div class="slide-content">
                        <h1 class="display-4 fw-bold mb-4">Home & Lifestyle</h1>
                        <p class="lead mb-5">Transform your living space with our curated home collection.</p>
                        <div class="d-flex gap-3">
                            <a href="products.php?category=home" class="btn btn-light btn-hero">Shop Home</a>
                            <a href="products.php" class="btn btn-outline-light btn-hero">All Products</a>
                        </div>
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
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4 rounded-3 bg-white shadow-sm h-100">
                        <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4 class="h5">Free Shipping</h4>
                        <p class="mb-0 text-muted">On orders over $50</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-3 bg-white shadow-sm h-100">
                        <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h4 class="h5">Easy Returns</h4>
                        <p class="mb-0 text-muted">30-day return policy</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-3 bg-white shadow-sm h-100">
                        <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="h5">Secure Payment</h4>
                        <p class="mb-0 text-muted">100% secure checkout</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="featured" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold">Featured Products</h2>
                <p class="text-muted">Handpicked selection of our best items</p>
            </div>
            <div class="row g-4">
                <?php
                $stmt = $pdo->query("SELECT * FROM products WHERE featured = 1 LIMIT 4");
                while($product = $stmt->fetch()): ?>
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <img src="<?php echo !empty($product['image']) ? 'get_image.php?id=' . $product['id'] : 'assets/images/placeholder.jpg'; ?>" 
                                     class="card-img-top h-100 object-fit-cover" 
                                     alt="<?php echo clean($product['name']); ?>">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm">
                                        <i class="far fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo clean($product['name']); ?></h5>
                                <div class="mt-auto">
                                    <p class="card-text fw-bold text-primary mb-2">$<?php echo number_format($product['price'], 2); ?></p>
                                    <div class="d-flex gap-2">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary btn-sm flex-grow-1">View Details</a>
                                        <button class="btn btn-primary btn-sm">
                                            <i class="fas fa-shopping-cart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center mt-5">
                <a href="products.php" class="btn btn-outline-primary px-4">View All Products</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container py-3">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h3 class="h2 mb-3">Subscribe to Our Newsletter</h3>
                    <p class="mb-4">Get the latest updates on new products and upcoming sales</p>
                    <form class="row g-2 justify-content-center">
                        <div class="col-md-8">
                            <input type="email" class="form-control form-control-lg" placeholder="Your email address">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-light btn-lg w-100">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="mb-3">E-Shop</h5>
                    <p class="text-muted">Your trusted online shopping destination for quality products at affordable prices.</p>
                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h5 class="mb-3">Shop</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="products.php" class="text-muted text-decoration-none">All Products</a></li>
                        <li class="mb-2"><a href="products.php?category=men" class="text-muted text-decoration-none">Men</a></li>
                        <li class="mb-2"><a href="products.php?category=women" class="text-muted text-decoration-none">Women</a></li>
                        <li class="mb-2"><a href="products.php?category=electronics" class="text-muted text-decoration-none">Electronics</a></li>
                        <li class="mb-2"><a href="products.php?category=accessories" class="text-muted text-decoration-none">Accessories</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h5 class="mb-3">Help</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="faq.php" class="text-muted text-decoration-none">FAQs</a></li>
                        <li class="mb-2"><a href="shipping.php" class="text-muted text-decoration-none">Shipping</a></li>
                        <li class="mb-2"><a href="returns.php" class="text-muted text-decoration-none">Returns</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Shop Street, City, Country</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@eshop.com</li>
                    </ul>
                    <h6 class="mt-4 mb-3">Payment Methods</h6>
                    <div class="d-flex gap-2">
                        <img src="assets/images/payment/visa.svg" alt="Visa" style="height: 24px;">
                        <img src="assets/images/payment/mastercard.svg" alt="Mastercard" style="height: 24px;">
                        <img src="assets/images/payment/paypal.svg" alt="PayPal" style="height: 24px;">
                        <img src="assets/images/payment/apple-pay.svg" alt="Apple Pay" style="height: 24px;">
                    </div>
                </div>
            </div>
            <hr class="my-4 border-secondary">
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
        });
    </script>
</body>
</html>