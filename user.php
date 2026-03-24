<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

echo "User page works successfully";
?>