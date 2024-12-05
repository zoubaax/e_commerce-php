<?php
require_once '../config/config.php';
require_once 'auth.php';
require_admin();

$success = $error = '';

// Handle user deletion
if (isset($_POST['delete']) && isset($_POST['user_id'])) {
    // Prevent deleting self
    if ($_POST['user_id'] == $_SESSION['user_id']) {
        $error = "You cannot delete your own account";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$_POST['user_id']])) {
            $success = "User deleted successfully";
        } else {
            $error = "Error deleting user";
        }
    }
}

// Handle user creation/update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['delete'])) {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if (empty($username) || empty($email)) {
        $error = "Username and email are required";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $_POST['id'] ?? 0]);
        if ($stmt->fetch()) {
            $error = "Username or email already exists";
        } else {
            if (isset($_POST['id'])) {
                // Update existing user
                if (!empty($password)) {
                    // Update with new password
                    $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, is_admin = ?, password = ? WHERE id = ?";
                    $params = [$username, $email, $full_name, $is_admin, password_hash($password, PASSWORD_DEFAULT), $_POST['id']];
                } else {
                    // Update without changing password
                    $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, is_admin = ? WHERE id = ?";
                    $params = [$username, $email, $full_name, $is_admin, $_POST['id']];
                }
                
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute($params)) {
                    $success = "User updated successfully";
                } else {
                    $error = "Error updating user";
                }
            } else {
                // Create new user
                if (empty($password)) {
                    $error = "Password is required for new users";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, is_admin) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $full_name, $is_admin])) {
                        $success = "User created successfully";
                    } else {
                        $error = "Error creating user";
                    }
                }
            }
        }
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, email, full_name, is_admin, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dark-theme.css">
</head>
<body>
    <div class="admin-dashboard">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Admin Dashboard</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categories.php">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">Orders</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">Users</a>
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
                <h1>Manage Users</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    Add New User
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
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['is_admin'] ? 'danger' : 'success'; ?>">
                                    <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-user" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#userModal"
                                        data-user='<?php echo json_encode($user); ?>'>
                                    Edit
                                </button>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="userForm">
                        <input type="hidden" name="id" id="userId">
                        
                        <div class="mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password <span class="text-muted">(Leave empty to keep current password)</span></label>
                            <input type="password" class="form-control" name="password" id="passwordField">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name">
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_admin" id="isAdmin">
                            <label class="form-check-label" for="isAdmin">Admin Access</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
        // Handle edit user
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', function() {
                const user = JSON.parse(this.dataset.user);
                const form = document.getElementById('userForm');
                
                form.id.value = user.id;
                form.username.value = user.username;
                form.email.value = user.email;
                form.full_name.value = user.full_name;
                form.is_admin.checked = user.is_admin == 1;
                
                // Clear password field for editing
                form.password.value = '';
                document.querySelector('.modal-title').textContent = 'Edit User';
            });
        });

        // Clear form when adding new user
        document.querySelector('[data-bs-target="#userModal"]').addEventListener('click', function() {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordField').required = true;
            document.querySelector('.modal-title').textContent = 'Add New User';
        });

        // Handle password requirement
        document.getElementById('userForm').addEventListener('submit', function(e) {
            const userId = document.getElementById('userId').value;
            const password = document.getElementById('passwordField').value;
            
            if (!userId && !password) {
                e.preventDefault();
                alert('Password is required for new users');
            }
        });
    </script>
</body>
</html>
