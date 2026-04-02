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

/* نتحقق هل الوصفة موجودة بالفعل في favourites */
$sqlCheck = "SELECT * FROM favourites
             WHERE userID = $userID AND recipeID = $recipeID";

$resultCheck = mysqli_query($conn, $sqlCheck);

if (!$resultCheck) {
    die("Check query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($resultCheck) > 0) {
    /* موجودة -> نحذفها */
    $sqlDelete = "DELETE FROM favourites
                  WHERE userID = $userID AND recipeID = $recipeID";

    $resultDelete = mysqli_query($conn, $sqlDelete);

    if (!$resultDelete) {
        die("Delete query failed: " . mysqli_error($conn));
    }

} else {
    /* غير موجودة -> نضيفها */
    $sqlInsert = "INSERT INTO favourites (userID, recipeID)
                  VALUES ($userID, $recipeID)";

    $resultInsert = mysqli_query($conn, $sqlInsert);

    if (!$resultInsert) {
        die("Insert query failed: " . mysqli_error($conn));
    }
}

/* رجوع لنفس صفحة الوصفة */
header("Location: view-recipe.php?id=" . $recipeID);
exit();
?>