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

// ===== Insert Recipe first (to get the recipeID) =====
// We insert with empty photo/video first, then update after we have the ID
$stmt = $conn->prepare(
    "INSERT INTO recipe (userID, categoryID, name, description, photoFileName, videoFilePath)
     VALUES (?, ?, ?, ?, '', '')"
);
$stmt->bind_param("iiss", $userID, $categoryID, $name, $description);

if (!$stmt->execute()) {
    die("Failed to insert recipe: " . $stmt->error);
}

$recipeID = $conn->insert_id;
$stmt->close();

// ===== Handle Video Upload — named with recipeID (optional) =====
$videoFilePath = '';

if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
    $originalVideoName = basename($_FILES['video']['name']);
    $videoExt = pathinfo($originalVideoName, PATHINFO_EXTENSION);

    $videoFilePath = "recipe_video_" . $recipeID . "." . $videoExt;
    $destination = "videos/" . $videoFilePath;

    if (!is_dir("videos")) {
        mkdir("videos", 0777, true);
    }

    if (!move_uploaded_file($_FILES['video']['tmp_name'], $destination)) {
        die("Failed to upload video.");
    }
}
$stmtUpdate = $conn->prepare(
    "UPDATE recipe SET photoFileName = ?, videoFilePath = ? WHERE id = ?"
);
$stmtUpdate->bind_param("ssi", $photoFileName, $videoFilePath, $recipeID);

// ===== Update recipe row with the actual file names =====
$stmtUpdate = $conn->prepare(
    "UPDATE recipe SET photoFileName = ?, videoFilePath = ? WHERE id = ?"
);
$stmtUpdate->bind_param("ssi", $photoFileName, $videoFilePath, $recipeID);
if (!$stmtUpdate->execute()) {
    die("Failed to update file names: " . $stmtUpdate->error);
}
$stmtUpdate->close();

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