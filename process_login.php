<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

$email = $_POST['emailAddress'];
$password = $_POST['password'];

$checkBlocked = "SELECT * FROM blocked_users WHERE emailAddress = '$email'";
$resultBlocked = $conn->query($checkBlocked);

if ($resultBlocked->num_rows > 0) {
    header("Location: login.php?error=You are blocked");
    exit();
}

$sql = "SELECT * FROM users WHERE emailAddress = '$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        $_SESSION['userID'] = $user['id'];
        $_SESSION['userType'] = $user['userType'];

        if ($user['userType'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: user.php");
        }
        exit();

    } else {
        header("Location: login.php?error=Wrong password");
        exit();
    }

} else {
    header("Location: login.php?error=Email not found");
    exit();
}
?>
