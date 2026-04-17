<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

$recipeID    = $_POST['recipeID']    ?? null;
$name        = $_POST['name']        ?? '';
$categoryID  = (int)($_POST['categoryID'] ?? 0);
$description = $_POST['description'] ?? '';

if (!$recipeID || empty($name) || $categoryID === 0 || empty($description)) {
    header("Location: my-recipes.php");
    exit();
}

$recipeID = (int) $recipeID;

$stmtCheck = $conn->prepare(
    "SELECT id, photoFileName, videoFilePath FROM recipe WHERE id = ? AND userID = ?"
);
$stmtCheck->bind_param("ii", $recipeID, $userID);
$stmtCheck->execute();
$existing = $stmtCheck->get_result()->fetch_assoc();
$stmtCheck->close();

if (!$existing) {
    header("Location: my-recipes.php");
    exit();
}

$photoFileName = $existing['photoFileName']; // keep old by default

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $oldPhotoPath = "images/" . $existing['photoFileName'];
    if (!empty($existing['photoFileName']) && file_exists($oldPhotoPath)) {
        unlink($oldPhotoPath);
    }
    $originalName  = basename($_FILES['photo']['name']);
    $photoFileName = $recipeID . '_' . $originalName;
    move_uploaded_file($_FILES['photo']['tmp_name'], "images/" . $photoFileName);
}

$videoFilePath = $existing['videoFilePath']; // keep old by default

if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
    $oldVideoPath = "videos/" . $existing['videoFilePath'];
    if (!empty($existing['videoFilePath']) && file_exists($oldVideoPath)) {
        unlink($oldVideoPath);
    }
    $originalVideoName = basename($_FILES['video']['name']);
    $videoFilePath     = $recipeID . '_' . $originalVideoName;
    move_uploaded_file($_FILES['video']['tmp_name'], "videos/" . $videoFilePath);
}

$stmtUpdate = $conn->prepare(
    "UPDATE recipe
     SET name = ?, categoryID = ?, description = ?, photoFileName = ?, videoFilePath = ?
     WHERE id = ? AND userID = ?"
);
$stmtUpdate->bind_param("sisssii", $name, $categoryID, $description, $photoFileName, $videoFilePath, $recipeID, $userID);

if (!$stmtUpdate->execute()) {
    die("Update failed: " . $stmtUpdate->error);
}
$stmtUpdate->close();

$conn->query("DELETE FROM ingredients WHERE recipeID = $recipeID");

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

$conn->query("DELETE FROM instructions WHERE recipeID = $recipeID");

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

header("Location: my-recipes.php");
exit();
?>
