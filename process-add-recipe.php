<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

$name        = $_POST['name'] ?? '';
$categoryID  = $_POST['categoryID'] ?? '';
$description = $_POST['description'] ?? '';

if (empty($name) || empty($categoryID) || empty($description)) {
    header("Location: add-recipe.php");
    exit();
}

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

$photoFileName = '';

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $originalPhotoName = basename($_FILES['photo']['name']);
    $photoExt = pathinfo($originalPhotoName, PATHINFO_EXTENSION);

    $photoFileName = "recipe_" . $recipeID . "." . $photoExt;
    $photoDestination = "images/" . $photoFileName;

    if (!is_dir("images")) {
        mkdir("images", 0777, true);
    }

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photoDestination)) {
        die("Failed to upload photo.");
    }
}

$videoFilePath = '';

if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
    $originalVideoName = basename($_FILES['video']['name']);
    $videoExt = pathinfo($originalVideoName, PATHINFO_EXTENSION);

    $videoFilePath = "recipe_video_" . $recipeID . "." . $videoExt;
    $videoDestination = "videos/" . $videoFilePath;

    if (!is_dir("videos")) {
        mkdir("videos", 0777, true);
    }

    if (!move_uploaded_file($_FILES['video']['tmp_name'], $videoDestination)) {
        die("Failed to upload video.");
    }
}

$stmtUpdate = $conn->prepare(
    "UPDATE recipe SET photoFileName = ?, videoFilePath = ? WHERE id = ?"
);
$stmtUpdate->bind_param("ssi", $photoFileName, $videoFilePath, $recipeID);

if (!$stmtUpdate->execute()) {
    die("Failed to update file names: " . $stmtUpdate->error);
}
$stmtUpdate->close();

$ingredientNames      = $_POST['ingredientName'] ?? [];
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

$steps = $_POST['step'] ?? [];

for ($i = 0; $i < count($steps); $i++) {
    $step = trim($steps[$i]);
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

header("Location: my-recipes.php");
exit();
?>
