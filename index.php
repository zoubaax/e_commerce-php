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
        .hero-section {
            position: relative;
            overflow: hidden;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6));
            color: white;
            padding: 100px 0;
            background-size: cover;
            background-position: center;
            background-image: url('https://img.freepik.com/free-photo/happy-beautiful-couple-posing-with-shopping-bags-violet_496169-2215.jpg?t=st=1749745419~exp=1749749019~hmac=11665155ea15398734a98dd50e7c3cfa64a9fc883e7847d6866e45a11d9a32e5&w=1380');
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
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
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section with Online Photo -->
    <section class="hero-section">
        <div class="container py-5">
            <div class="row justify-content-center text-center hero-content">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Discover Amazing Products</h1>
                    <p class="lead mb-5">Shop the latest trends at unbeatable prices with fast, reliable delivery</p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="products.php" class="btn btn-light btn-hero">Shop Now</a>
                        <a href="#featured" class="btn btn-outline-light btn-hero">Featured Products</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="p-4 rounded-3 bg-white shadow-sm">
                        <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4 class="h5">Free Shipping</h4>
                        <p class="mb-0 text-muted">On orders over $50</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-3 bg-white shadow-sm">
                        <div class="icon-lg bg-primary bg-opacity-10 text-primary rounded-circle mb-3 mx-auto">
                            <i class="fas fa-undo-alt"></i>
                        </div>
                        <h4 class="h5">Easy Returns</h4>
                        <p class="mb-0 text-muted">30-day return policy</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-3 bg-white shadow-sm">
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
</body>
</html>