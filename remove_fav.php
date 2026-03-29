<?php
session_start();
include "db.php";

// تأكد المستخدم مسجل دخول
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

// تأكد فيه ID
if (isset($_GET['id'])) {

    $recipeID = $_GET['id'];

    // حذف من الفافوريت
    $deleteQuery = "
    DELETE FROM favourites 
    WHERE userID = $userID AND recipeID = $recipeID
    ";

    $conn->query($deleteQuery);
}

// رجوع لصفحة المستخدم
header("Location: user.php");
exit();
?>