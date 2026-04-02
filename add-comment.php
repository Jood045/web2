<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$recipeID = $_POST['recipeID'] ?? null;
$comment = $_POST['comment'] ?? null;

if ($recipeID == null || $comment == null) {
    header("Location: view-recipe.php?id=" . $recipeID);
    exit();
}

$sql = "INSERT INTO comment (recipeID, userID, comment, date)
        VALUES ($recipeID, $userID, '$comment', NOW())";

if (!mysqli_query($conn, $sql)) {
    die("Insert failed: " . mysqli_error($conn));
}

header("Location: view-recipe.php?id=" . $recipeID);
exit();
?>