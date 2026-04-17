<?php

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my-recipes.php");
    exit();
}

$recipeID = (int)$_GET['id'];

include "db.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$check = $conn->prepare("SELECT id, photoFileName, videoFilePath FROM recipe WHERE id = ? AND userID = ?");
$check->bind_param("ii", $recipeID, $userID);
$check->execute();
$recipe = $check->get_result()->fetch_assoc();

if (!$recipe) {
    header("Location: my-recipes.php");
    exit();
}

$photoPath = "uploads/recipes/" . $recipe['photoFileName'];
$videoPath = "uploads/videos/" . $recipe['videoFilePath'];

if (!empty($recipe['photoFileName']) && file_exists($photoPath)) {
    unlink($photoPath);
}

if (!empty($recipe['videoFilePath']) && file_exists($videoPath)) {
    unlink($videoPath);
}

$conn->query("DELETE FROM likes        WHERE recipeID = $recipeID");
$conn->query("DELETE FROM favourites   WHERE recipeID = $recipeID");
$conn->query("DELETE FROM comment      WHERE recipeID = $recipeID");
$conn->query("DELETE FROM report       WHERE recipeID = $recipeID");
$conn->query("DELETE FROM ingredients  WHERE recipeID = $recipeID");
$conn->query("DELETE FROM instructions WHERE recipeID = $recipeID");

$conn->query("DELETE FROM recipe WHERE id = $recipeID");

$conn->close();
header("Location: my-recipes.php");
exit();
?>
