<?php
session_start();
include "db.php";

// البيانات من الفورم
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['emailAddress'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// 1) نحفظ المستخدم بدون صورة
$sql = "INSERT INTO users (userType, firstName, lastName, emailAddress, password, photoFileName)
        VALUES ('user', '$firstName', '$lastName', '$email', '$password', 'default.png')";

$conn->query($sql);

// 2) نجيب ID المستخدم الجديد
$userID = $conn->insert_id;

// 3) رفع الصورة
if ($_FILES['photo']['name'] != "") {

    $originalName = $_FILES['photo']['name'];

    // نفس المثال: 5_lama.jpg
    $photoName = $userID . "_" . $originalName;

    move_uploaded_file($_FILES['photo']['tmp_name'], "images/" . $photoName);

} else {
    $photoName = "default.png";
}

// 4) نحدث اسم الصورة في الداتابيس
$sqlUpdate = "UPDATE users SET photoFileName = '$photoName' WHERE id = $userID";
$conn->query($sqlUpdate);

// 5) نحفظ session
$_SESSION['userID'] = $userID;
$_SESSION['userType'] = "user";

// 6) تحويل لصفحة المستخدم
header("Location: user.php");
exit();
?>