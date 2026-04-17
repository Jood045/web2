<?php
session_start();
include "db.php";

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['emailAddress'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (userType, firstName, lastName, emailAddress, password, photoFileName)
        VALUES ('user', '$firstName', '$lastName', '$email', '$password', 'default.png')";

$conn->query($sql);

$userID = $conn->insert_id;

if ($_FILES['photo']['name'] != "") {

    $originalName = $_FILES['photo']['name'];

    $photoName = $userID . "_" . $originalName;

    move_uploaded_file($_FILES['photo']['tmp_name'], "images/" . $photoName);

} else {
    $photoName = "default.png";
}

$sqlUpdate = "UPDATE users SET photoFileName = '$photoName' WHERE id = $userID";
$conn->query($sqlUpdate);

$_SESSION['userID'] = $userID;
$_SESSION['userType'] = "user";

header("Location: user.php");
exit();
?>
