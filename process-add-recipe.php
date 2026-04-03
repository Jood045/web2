<?php
session_start();
include "db.php";

// Only logged-in regular users
if (!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

// ===== Get form data =====
$name        = $_POST['name']        ?? '';
$categoryID  = $_POST['categoryID']  ?? '';
$description = $_POST['description'] ?? '';

if (empty($name) || empty($categoryID) || empty($description)) {
    header("Location: add-recipe.php");
    exit();
}

// ===== Handle Photo Upload =====
$photoFileName = '';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $originalName  = basename($_FILES['photo']['name']);
    $photoFileName = time() . '_' . $originalName;
    $destination   = "uploads/recipes/" . $photoFileName;
    move_uploaded_file($_FILES['photo']['tmp_name'], $destination);
}

// ===== Handle Video Upload (optional) =====
$videoFilePath = '';
if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
    $originalVideoName = basename($_FILES['video']['name']);
    $videoFilePath     = time() . '_' . $originalVideoName;
    $destination       = "uploads/videos/" . $videoFilePath;
    move_uploaded_file($_FILES['video']['tmp_name'], $destination);
}

// ===== Insert Recipe =====
$stmt = $conn->prepare(
    "INSERT INTO recipe (userID, categoryID, name, description, photoFileName, videoFilePath)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("iissss", $userID, $categoryID, $name, $description, $photoFileName, $videoFilePath);

if (!$stmt->execute()) {
    die("Failed to insert recipe: " . $stmt->error);
}

$recipeID = $conn->insert_id;
$stmt->close();

// ===== Insert Ingredients =====
$ingredientNames      = $_POST['ingredientName']     ?? [];
$ingredientQuantities = $_POST['ingredientQuantity'] ?? [];

for ($i = 0; $i < count($ingredientNames); $i++) {
    $ingName = trim($ingredientNames[$i]);
    $ingQty  = trim($ingredientQuantities[$i] ?? '');

    if ($ingName === '') continue;

    $stmtIng = $conn->prepare(
        "INSERT INTO ingredients (recipeID, ingredientName, ingredientQuantity) VALUES (?, ?, ?)"
    );
    $stmtIng->bind_param("iss", $recipeID, $ingName, $ingQty);
    $stmtIng->execute();
    $stmtIng->close();
}

// ===== Insert Instructions =====
$steps = $_POST['step'] ?? [];

for ($i = 0; $i < count($steps); $i++) {
    $step      = trim($steps[$i]);
    $stepOrder = $i + 1;

    if ($step === '') continue;

    $stmtStep = $conn->prepare(
        "INSERT INTO instructions (recipeID, step, stepOrder) VALUES (?, ?, ?)"
    );
    $stmtStep->bind_param("isi", $recipeID, $step, $stepOrder);
    $stmtStep->execute();
    $stmtStep->close();
}

$conn->close();

// ===== Redirect to My Recipes =====
header("Location: my-recipes.php");
exit();
?>
