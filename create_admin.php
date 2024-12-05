<?php
require_once 'config/config.php';

// Admin user details
$admin_username = 'admin';
$admin_email = 'admin@example.com';
$admin_password = 'admin123'; // You should change this password
$admin_fullname = 'Administrator';

// Check if admin already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
$stmt->execute([$admin_email, $admin_username]);
$existing_admin = $stmt->fetch();

if ($existing_admin) {
    echo "Admin user already exists!";
} else {
    // Create admin user
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, is_admin) VALUES (?, ?, ?, ?, 1)");
    if ($stmt->execute([$admin_username, $admin_email, $hashed_password, $admin_fullname])) {
        echo "Admin user created successfully!<br>";
        echo "Username: " . $admin_username . "<br>";
        echo "Password: " . $admin_password . "<br>";
        echo "<a href='login.php'>Go to Login</a>";
    } else {
        echo "Error creating admin user.";
    }
}
?>
