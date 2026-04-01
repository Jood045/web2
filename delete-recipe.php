<?php
// =====================
// delete-recipe.php
// =====================
session_start();

// 1. Check login
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

// 2. Get recipe ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my-recipes.php");
    exit();
}

$recipeID = (int)$_GET['id'];

// 3. Database connection
$conn = new mysqli("localhost", "root", "", "kidbites");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. Make sure this recipe belongs to the logged-in user (security check)
$check = $conn->prepare("SELECT id, photoFileName, videoFilePath FROM recipe WHERE id = ? AND userID = ?");
$check->bind_param("ii", $recipeID, $userID);
$check->execute();
$recipe = $check->get_result()->fetch_assoc();

if (!$recipe) {
    // Recipe not found or doesn't belong to this user
    header("Location: my-recipes.php");
    exit();
}

// 5. Delete physical files from server
$photoPath = "uploads/recipes/" . $recipe['photoFileName'];
$videoPath = "uploads/videos/" . $recipe['videoFilePath'];

if (!empty($recipe['photoFileName']) && file_exists($photoPath)) {
    unlink($photoPath);
}

if (!empty($recipe['videoFilePath']) && file_exists($videoPath)) {
    unlink($videoPath);
}

// 6. Delete all related data from database (order matters due to foreign keys)
$conn->query("DELETE FROM likes        WHERE recipeID = $recipeID");
$conn->query("DELETE FROM favourites   WHERE recipeID = $recipeID");
$conn->query("DELETE FROM comment      WHERE recipeID = $recipeID");
$conn->query("DELETE FROM report       WHERE recipeID = $recipeID");
$conn->query("DELETE FROM ingredients  WHERE recipeID = $recipeID");
$conn->query("DELETE FROM instructions WHERE recipeID = $recipeID");

// 7. Delete the recipe itself
$conn->query("DELETE FROM recipe WHERE id = $recipeID");

// 8. Redirect back to My Recipes
$conn->close();
header("Location: my-recipes.php");
exit();
?>
