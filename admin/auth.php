<?php
function require_admin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header('Location: ../login.php');
        exit();
    }
}
?>
