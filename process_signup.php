<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

// استلام البيانات
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['emailAddress'];
$password = $_POST['password'];

// التحقق إذا المستخدم محظور
$checkBlocked = "SELECT * FROM blocked_users WHERE emailAddress = '$email'";
$resultBlocked = $conn->query($checkBlocked);

if ($resultBlocked->num_rows > 0) {
    header("Location: signup.php?error=You are blocked");
    exit();
}

// 1) التحقق هل الإيميل موجود مسبقاً في users
$checkEmail = "SELECT * FROM users WHERE emailAddress = '$email'";
$result = $conn->query($checkEmail);

if ($result->num_rows > 0) {
    header("Location: signup.php?error=Email already registered");
    exit();
}

// 2) تشفير كلمة المرور
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// صورة افتراضية
$photoName = "user.jpg";

// إذا المستخدم رفع صورة
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $originalName = $_FILES['photo']['name'];
    $tempName = $_FILES['photo']['tmp_name'];

    // نخلي اسم الصورة فريد
    $photoName = time() . "_" . $originalName;

    // مسار الحفظ
    $destination = "images/" . $photoName;

    // نقل الصورة إلى مجلد images
    move_uploaded_file($tempName, $destination);
}

// 4) إدخال المستخدم الجديد
$sql = "INSERT INTO users (userType, firstName, lastName, emailAddress, password, photoFileName)
VALUES ('user', '$firstName', '$lastName', '$email', '$hashedPassword', '$photoName')";

if ($conn->query($sql) === TRUE) {

    $userID = $conn->insert_id;

    $_SESSION['userID'] = $userID;
    $_SESSION['userType'] = "user";

    header("Location: signup.php");
    exit();

} else {
    echo "Error: " . $conn->error;
}
?>
