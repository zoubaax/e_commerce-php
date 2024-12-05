<?php
require_once 'config/config.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if (!$product_id) {
    $_SESSION['error'] = 'Invalid product ID';
    redirect('products.php');
}

// Fetch product details with category name
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    redirect('products.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo clean($product['name']); ?> - E-Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-theme.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo !empty($product['image']) ? 'get_image.php?id=' . $product['id'] : 'assets/images/placeholder.jpg'; ?>" 
                     alt="<?php echo clean($product['name']); ?>" 
                     class="img-fluid rounded">
            </div>
            <div class="col-md-6">
                <h1><?php echo clean($product['name']); ?></h1>
                <p class="text-muted">Category: <?php echo clean($product['category_name']); ?></p>
                <h2 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h2>
                <p class="my-4"><?php echo nl2br(clean($product['description'])); ?></p>
                
                <?php if ($product['stock'] > 0): ?>
                    <p class="text-success">In Stock (<?php echo $product['stock']; ?> available)</p>
                    <form action="cart_actions.php" method="POST" class="d-flex align-items-center gap-3">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="action" value="add">
                        <div class="input-group" style="width: 150px;">
                            <label class="input-group-text" for="quantity">Qty</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-danger">Out of Stock</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>
