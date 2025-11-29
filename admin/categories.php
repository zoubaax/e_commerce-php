<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

$success = $error = '';

// Handle category deletion
if (isset($_POST['delete']) && isset($_POST['category_id'])) {
    // Check if category has products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$_POST['category_id']]);
    $productCount = $stmt->fetchColumn();

    if ($productCount > 0) {
        $error = "Cannot delete category: it contains products";
    } else {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$_POST['category_id']])) {
            $success = "Category deleted successfully";
        } else {
            $error = "Error deleting category";
        }
    }
}

// Handle category creation/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($name)) {
        $error = "Category name is required";
    } else {
        if (isset($_POST['id'])) {
            // Update existing category
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            if ($stmt->execute([$name, $description, $_POST['id']])) {
                $success = "Category updated successfully";
            } else {
                $error = "Error updating category";
            }
        } else {
            // Create new category
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            if ($stmt->execute([$name, $description])) {
                $success = "Category added successfully";
            } else {
                $error = "Error adding category";
            }
        }
    }
}

// Fetch all categories with product count
$stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                     FROM categories c 
                     LEFT JOIN products p ON c.id = p.category_id 
                     GROUP BY c.id 
                     ORDER BY c.name");
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-white">Categories Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="fas fa-plus"></i> Add New Category
            </button>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td><?php echo htmlspecialchars($category['description']); ?></td>
                        <td><?php echo $category['product_count']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-category" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#categoryModal"
                                    data-category='<?php echo json_encode($category); ?>'>
                                Edit
                            </button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal">
        <div class="modal-dialog">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Add/Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-white">
                    <form method="POST" id="categoryForm">
                        <input type="hidden" name="id" id="categoryId">
                        
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', function() {
                const category = JSON.parse(this.dataset.category);
                const form = document.getElementById('categoryForm');
                
                form.id.value = category.id;
                form.name.value = category.name;
                form.description.value = category.description;
            });
        });

        document.querySelector('[data-bs-target="#categoryModal"]').addEventListener('click', function() {
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryId').value = '';
        });
    </script>
</body>
</html>