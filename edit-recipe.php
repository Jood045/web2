<?php
session_start();
include "db.php";

// Only logged-in regular users
if (!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];

// ===== Check recipe ID in query string =====
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my-recipes.php");
    exit();
}

$recipeID = (int) $_GET['id'];

// ===== Fetch recipe — must belong to this user =====
$stmtRecipe = $conn->prepare(
    "SELECT * FROM recipe WHERE id = ? AND userID = ?"
);
$stmtRecipe->bind_param("ii", $recipeID, $userID);
$stmtRecipe->execute();
$recipe = $stmtRecipe->get_result()->fetch_assoc();

if (!$recipe) {
    // Recipe not found or doesn't belong to this user
    header("Location: my-recipes.php");
    exit();
}

// ===== Fetch categories =====
$categoriesResult = $conn->query("SELECT * FROM recipecategory");

// ===== Fetch ingredients =====
$ingredientsResult = $conn->query(
    "SELECT * FROM ingredients WHERE recipeID = $recipeID"
);
$ingredients = [];
while ($row = $ingredientsResult->fetch_assoc()) {
    $ingredients[] = $row;
}

// ===== Fetch instructions =====
$instructionsResult = $conn->query(
    "SELECT * FROM instructions WHERE recipeID = $recipeID ORDER BY stepOrder ASC"
);
$instructions = [];
while ($row = $instructionsResult->fetch_assoc()) {
    $instructions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KidBites | Edit Recipe</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="container header-inner">
        <a class="brand" href="index.php">
            <img src="images/logo.png" alt="KidBites Logo" class="logo">
        </a>
        <nav class="nav">
            <a href="user.php">User page</a>
            <a href="my-recipes.php">My Recipes</a>
            <a href="index.php">Sign Out</a>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <section class="card" style="max-width:700px; margin:auto;">
            <h2>Edit Recipe</h2>
            <p class="hint">Update the details below and save your changes.</p>

            <form action="process-edit-recipe.php" method="POST" enctype="multipart/form-data">

                <!-- Hidden recipe ID -->
                <input type="hidden" name="recipeID" value="<?= $recipeID ?>">

                <!-- Recipe Name -->
                <div class="field">
                    <label>Recipe Name</label>
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($recipe['name']) ?>" required>
                </div>

                <!-- Category -->
                <div class="field">
                    <label>Category</label>
                    <select name="categoryID" required>
                        <option value="">-- Select a Category --</option>
                        <?php while ($cat = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= ($cat['id'] == $recipe['categoryID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['categoryName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="field">
                    <label>Description</label>
                    <textarea name="description" rows="3" required><?= htmlspecialchars($recipe['description']) ?></textarea>
                </div>

                <!-- Current Photo -->
                <div class="field">
                    <label>Current Photo</label>
                    <?php if (!empty($recipe['photoFileName'])): ?>
                        <div style="margin-bottom:10px;">
                            <img src="uploads/recipes/<?= htmlspecialchars($recipe['photoFileName']) ?>"
                                 alt="Current photo"
                                 style="width:180px; border-radius:12px; border:1px solid #d9e2c9;">
                        </div>
                    <?php endif; ?>
                    <label>Upload New Photo (leave empty to keep current)</label>
                    <input type="file" name="photo" accept="image/*">
                </div>

                <!-- Current Video -->
                <div class="field">
                    <label>Current Video</label>
                    <?php if (!empty($recipe['videoFilePath'])): ?>
                        <p class="hint">
                            Current video: <strong><?= htmlspecialchars($recipe['videoFilePath']) ?></strong>
                        </p>
                    <?php else: ?>
                        <p class="hint">No video uploaded.</p>
                    <?php endif; ?>
                    <label>Upload New Video (leave empty to keep current)</label>
                    <input type="file" name="video" accept="video/*">
                </div>

                <!-- Ingredients -->
                <div class="field">
                    <label>Ingredients</label>
                    <div id="ingredientsContainer">
                        <?php foreach ($ingredients as $i => $ing): ?>
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span style="font-size:13px; font-weight:700; min-width:90px;">
                                    Ingredient <?= $i + 1 ?>:
                                </span>
                                <input type="text" name="ingredientName[]"
                                       value="<?= htmlspecialchars($ing['ingredientName']) ?>"
                                       required style="flex:1;">
                                <input type="text" name="ingredientQuantity[]"
                                       value="<?= htmlspecialchars($ing['ingredientQuantity']) ?>"
                                       required style="flex:1;">
                                <button type="button" onclick="removeItem(this, 'ingredient')"
                                    style="background:#d8a7b1;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;">
                                    Remove
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addIngredient()" style="margin-top:8px;">
                        + Add Ingredient
                    </button>
                </div>

                <!-- Instructions -->
                <div class="field">
                    <label>Instructions</label>
                    <div id="stepsContainer">
                        <?php foreach ($instructions as $i => $inst): ?>
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span style="font-size:13px; font-weight:700; min-width:60px;">
                                    Step <?= $i + 1 ?>:
                                </span>
                                <input type="text" name="step[]"
                                       value="<?= htmlspecialchars($inst['step']) ?>"
                                       required style="flex:1;">
                                <button type="button" onclick="removeItem(this, 'step')"
                                    style="background:#d8a7b1;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;">
                                    Remove
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addStep()" style="margin-top:8px;">
                        + Add Step
                    </button>
                </div>

                <!-- Submit -->
                <div class="btn-row" style="justify-content:center; margin-top:20px;">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a class="btn" href="my-recipes.php" style="text-decoration:none;">Cancel</a>
                </div>

            </form>
        </section>
    </div>
</main>

<footer class="footer">
    © 2026 KidBites
</footer>

<script>
function addIngredient() {
    let container = document.getElementById("ingredientsContainer");
    let count = container.children.length + 1;
    let div = document.createElement("div");
    div.style.cssText = "display:flex; align-items:center; gap:8px; margin-bottom:8px;";
    div.innerHTML = `
        <span style="font-size:13px; font-weight:700; min-width:90px;">Ingredient ${count}:</span>
        <input type="text" name="ingredientName[]" placeholder="e.g., flour" required style="flex:1;">
        <input type="text" name="ingredientQuantity[]" placeholder="e.g., 1 cup" required style="flex:1;">
        <button type="button" onclick="removeItem(this, 'ingredient')"
            style="background:#d8a7b1;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;">
            Remove
        </button>
    `;
    container.appendChild(div);
}

function addStep() {
    let container = document.getElementById("stepsContainer");
    let count = container.children.length + 1;
    let div = document.createElement("div");
    div.style.cssText = "display:flex; align-items:center; gap:8px; margin-bottom:8px;";
    div.innerHTML = `
        <span style="font-size:13px; font-weight:700; min-width:60px;">Step ${count}:</span>
        <input type="text" name="step[]" placeholder="Describe this step..." required style="flex:1;">
        <button type="button" onclick="removeItem(this, 'step')"
            style="background:#d8a7b1;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;">
            Remove
        </button>
    `;
    container.appendChild(div);
}

function removeItem(button, type) {
    button.parentElement.remove();
    renumber(type);
}

function renumber(type) {
    if (type === 'ingredient') {
        let items = document.getElementById("ingredientsContainer").children;
        for (let i = 0; i < items.length; i++) {
            items[i].querySelector('span').textContent = `Ingredient ${i + 1}:`;
        }
    } else {
        let items = document.getElementById("stepsContainer").children;
        for (let i = 0; i < items.length; i++) {
            items[i].querySelector('span').textContent = `Step ${i + 1}:`;
        }
    }
}
</script>

</body>
</html>
