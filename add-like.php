<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$recipeID = $_POST['recipeID'] ?? null;

if ($recipeID == null) {
    header("Location: user.php");
    exit();
}

/* هل موجود مسبقًا؟ */
$sqlCheck = "SELECT *
             FROM likes
             WHERE userID = $userID AND recipeID = $recipeID";

$resultCheck = mysqli_query($conn, $sqlCheck);

if (!$resultCheck) {
    die("Check query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($resultCheck) > 0) {
    /* موجود -> احذفه */
    $sqlDelete = "DELETE FROM likes
                  WHERE userID = $userID AND recipeID = $recipeID";

    if (!mysqli_query($conn, $sqlDelete)) {
        die("Delete query failed: " . mysqli_error($conn));
    }
} else {
    /* غير موجود -> أضفه */
    $sqlInsert = "INSERT INTO likes (userID, recipeID)
                  VALUES ($userID, $recipeID)";

    if (!mysqli_query($conn, $sqlInsert)) {
        die("Insert query failed: " . mysqli_error($conn));
    }
}

header("Location: view-recipe.php?id=" . $recipeID);
exit();
?>