<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/config.php';
require_once 'auth.php';
require_admin();

// Test database connection and tables
try {
    // Test products table structure
    $stmt = $pdo->query("SHOW COLUMNS FROM products");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    error_log("Current table columns: " . print_r($columns, true));
    
    // Check if we need to update the table structure
    if (!in_array('image_type', $columns)) {
        error_log("Updating products table structure...");
        $pdo->exec("ALTER TABLE products DROP COLUMN IF EXISTS image");
        $pdo->exec("ALTER TABLE products 
                   ADD COLUMN IF NOT EXISTS image MEDIUMBLOB,
                   ADD COLUMN IF NOT EXISTS image_type VARCHAR(50)");
        error_log("Products table structure updated successfully");
    }
} catch (Exception $e) {
    error_log("Database structure error: " . $e->getMessage());
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = $error = '';

// Debug: Log POST data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));
}

// Handle product deletion
if (isset($_POST['delete']) && isset($_POST['product_id'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$_POST['product_id']])) {
        $success = "Product deleted successfully";
    } else {
        $error = "Error deleting product";
    }
}

// Handle product creation/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    error_log("=== Starting Product Form Submission ===");
    
    // Debug log all POST and FILES data
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token";
        error_log("CSRF token mismatch. Expected: " . $_SESSION['csrf_token'] . ", Received: " . ($_POST['csrf_token'] ?? 'none'));
    } else {
        try {
            $pdo->beginTransaction();
            
            // Prepare data
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
            $category_id = filter_var($_POST['category_id'] ?? '', FILTER_VALIDATE_INT);
            $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
            $featured = isset($_POST['featured']) ? 1 : 0;

            error_log("Processed form data:");
            error_log("Name: " . $name);
            error_log("Price: " . $price);
            error_log("Category ID: " . $category_id);
            error_log("Stock: " . $stock);
            error_log("Featured: " . $featured);

            // Validate data
            $errors = [];
            if (empty($name)) $errors[] = "Name is required";
            if ($price === false || $price <= 0) $errors[] = "Valid price is required";
            if ($category_id === false || $category_id <= 0) $errors[] = "Valid category is required";
            if ($stock === false || $stock < 0) $errors[] = "Valid stock quantity is required";

            if (!empty($errors)) {
                throw new Exception("Validation errors: " . implode(", ", $errors));
            }

            // Handle image upload
            $image = null;
            $image_type = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['image/jpeg', 'image/png', 'image/gif'];
                $file_type = $_FILES['image']['type'];
                
                error_log("Processing image upload. Type: " . $file_type);
                
                if (!in_array($file_type, $allowed)) {
                    throw new Exception("Invalid file type. Allowed types: jpg, png, gif");
                }
                
                $image = file_get_contents($_FILES['image']['tmp_name']);
                if ($image === false) {
                    throw new Exception("Failed to read image file");
                }
                $image_type = $file_type;
                error_log("Image processed successfully");
            }

            // Prepare SQL statement
            if (!isset($_POST['id'])) {
                // Create new product
                $sql = "INSERT INTO products (name, description, price, category_id, 
                        stock, featured, image, image_type, created_at) 
                        VALUES (:name, :description, :price, :category_id, :stock, 
                                :featured, :image, :image_type, NOW())";
                
                $params = [
                    ':name' => $name,
                    ':description' => $description,
                    ':price' => $price,
                    ':category_id' => $category_id,
                    ':stock' => $stock,
                    ':featured' => $featured,
                    ':image' => $image,
                    ':image_type' => $image_type
                ];
                
                error_log("Executing INSERT query with params: " . print_r($params, true));
                
                $stmt = $pdo->prepare($sql);
                if (!$stmt->execute($params)) {
                    throw new Exception("Error adding product: " . implode(", ", $stmt->errorInfo()));
                }
                $new_id = $pdo->lastInsertId();
                error_log("Product added successfully with ID: " . $new_id);
                $success = "Product added successfully with ID: " . $new_id;
            } else {
                // Update existing product
                $sql = "UPDATE products SET 
                        name = :name, 
                        description = :description, 
                        price = :price, 
                        category_id = :category_id, 
                        stock = :stock, 
                        featured = :featured";
                
                $params = [
                    ':name' => $name,
                    ':description' => $description,
                    ':price' => $price,
                    ':category_id' => $category_id,
                    ':stock' => $stock,
                    ':featured' => $featured,
                    ':id' => $_POST['id']
                ];

                if ($image !== null) {
                    $sql .= ", image = :image, image_type = :image_type";
                    $params[':image'] = $image;
                    $params[':image_type'] = $image_type;
                }

                $sql .= " WHERE id = :id";
                
                error_log("Executing UPDATE query: " . $sql);
                error_log("Update params: " . print_r($params, true));
                
                $stmt = $pdo->prepare($sql);
                if (!$stmt->execute($params)) {
                    throw new Exception("Error updating product: " . implode(", ", $stmt->errorInfo()));
                }
                $success = "Product updated successfully";
            }
            
            $pdo->commit();
            error_log("Transaction committed successfully");
            
            // Redirect to prevent form resubmission
            header("Location: products.php?success=" . urlencode($success));
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
            error_log("Error in product submission: " . $e->getMessage());
            if ($stmt ?? null) {
                error_log("Database error info: " . print_r($stmt->errorInfo(), true));
            }
        }
    }
}

// Display success message from redirect
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Fetch all products with their categories
$stmt = $pdo->query("SELECT p.id, p.name, p.description, p.price, p.category_id, 
                            p.stock, p.featured, p.created_at, c.name as category_name,
                            CASE WHEN p.image IS NOT NULL THEN 1 ELSE 0 END as has_image,
                            p.image_type
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC");
error_log("Found " . $stmt->rowCount() . " products");
$products = $stmt->fetchAll();

// Fetch categories for the form
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
error_log("Found " . count($categories) . " categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
</head>
<body class="admin-dashboard">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Products</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                Add New Product
            </button>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <?php if ($product['has_image']): ?>
                                <img src="get_image.php?id=<?php echo $product['id']; ?>" 
                                     class="img-thumbnail" alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <div class="text-center">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-product" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#productModal"
                                    data-product='<?php echo json_encode($product); ?>'>
                                Edit
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="products.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback">Please provide a product name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            <div class="invalid-feedback">Please provide a valid price.</div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                            <div class="invalid-feedback">Please provide a valid stock quantity.</div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">Supported formats: JPG, PNG, GIF</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="featured" name="featured">
                            <label class="form-check-label" for="featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add/Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="productForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" id="productId">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback">Please provide a product name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            <div class="invalid-feedback">Please provide a valid price.</div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                            <div class="invalid-feedback">Please provide a valid stock quantity.</div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">Supported formats: JPG, PNG, GIF</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="featured" name="featured">
                            <label class="form-check-label" for="featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
        // Handle edit product
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const product = JSON.parse(this.dataset.product);
                const form = document.getElementById('productForm');
                
                form.id.value = product.id;
                form.name.value = product.name;
                form.description.value = product.description;
                form.price.value = product.price;
                form.category_id.value = product.category_id;
                form.stock.value = product.stock;
                form.featured.checked = product.featured == 1;
            });
        });

        // Clear form when adding new product
        document.querySelector('[data-bs-target="#productModal"]').addEventListener('click', function() {
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
        });
    </script>
</body>
</html>
