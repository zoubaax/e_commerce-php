<?php
// admin/includes/auth.php
function require_admin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header('Location: ../login.php');
        exit();
    }
}
?>