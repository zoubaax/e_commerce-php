<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to access this page';
    redirect('login.php');
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method';
    redirect('account.php');
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = 'Invalid CSRF token';
    redirect('account.php');
}

// Get form data
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['error'] = 'All password fields are required';
    redirect('account.php');
}

// Check if new password matches confirmation
if ($new_password !== $confirm_password) {
    $_SESSION['error'] = 'New password and confirmation do not match';
    redirect('account.php');
}

// Validate password length and complexity
if (strlen($new_password) < 8) {
    $_SESSION['error'] = 'Password must be at least 8 characters long';
    redirect('account.php');
}

try {
    // Get user's current password hash from database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $_SESSION['error'] = 'Current password is incorrect';
        redirect('account.php');
    }

    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in database
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$password_hash, $_SESSION['user_id']]);

    $_SESSION['success'] = 'Password updated successfully';
    redirect('account.php');

} catch (PDOException $e) {
    error_log("Password update error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while updating your password. Please try again.';
    redirect('account.php');
}
