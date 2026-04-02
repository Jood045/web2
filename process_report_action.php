<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

if (!isset($_SESSION['userID']) || $_SESSION['userType'] != "admin") {
    header("Location: login.php?error=Access denied");
    exit();
}

$reportID = $_POST['reportID'];
$recipeID = $_POST['recipeID'];
$action = $_POST['action'];

if ($action == "dismiss") {
    $sql = "DELETE FROM report WHERE id = $reportID";
    $conn->query($sql);

    header("Location: admin.php");
    exit();
}

if ($action == "block") {
    // نجيب صاحب الوصفة
    $sqlUser = "SELECT users.id, users.firstName, users.lastName, users.emailAddress
                FROM recipe
                JOIN users ON recipe.userID = users.id
                WHERE recipe.id = $recipeID";
    $resultUser = $conn->query($sqlUser);
    $user = $resultUser->fetch_assoc();

    $blockedID = $user['id'];
    $firstName = $user['firstName'];
    $lastName = $user['lastName'];
    $email = $user['emailAddress'];

    // إضافة المستخدم إلى blocked_users
    $sqlInsertBlocked = "INSERT INTO blocked_users (firstName, lastName, emailAddress)
                         VALUES ('$firstName', '$lastName', '$email')";
    $conn->query($sqlInsertBlocked);

    // حذف التقرير الحالي
    $sqlDeleteReport = "DELETE FROM report WHERE id = $reportID";
    $conn->query($sqlDeleteReport);

    // حذف المستخدم من users
    $sqlDeleteUser = "DELETE FROM users WHERE id = $blockedID";
    $conn->query($sqlDeleteUser);

    header("Location: admin.php");
    exit();
}
?>