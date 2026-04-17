<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

if (isset($_GET['id'])) {

    $recipeID = $_GET['id'];

    $deleteQuery = "
    DELETE FROM favourites 
    WHERE userID = $userID AND recipeID = $recipeID
    ";

    $conn->query($deleteQuery);
}

header("Location: user.php");
exit();
?>
