<?php
session_start();
include "db.php";

// Only logged-in regular users can add recipes
if (!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user') {
    header("Location: login.php");
    exit();
}

// Fetch categories from database
$categoriesResult = $conn->query("SELECT * FROM recipecategory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>KidBites | Add Recipe</title>
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
            <h2>Add New Recipe</h2>
            <p class="hint">Fill in the details below to share your recipe.</p>

            <form action="process-add-recipe.php" method="POST" enctype="multipart/form-data">

                <!-- Recipe Name -->
                <div class="field">
                    <label>Recipe Name</label>
                    <input type="text" name="name" placeholder="e.g., Banana Pancakes" required>
                </div>

                <!-- Category -->
                <div class="field">
                    <label>Category</label>
                    <select name="categoryID" required>
                        <option value="">-- Select a Category --</option>
                        <?php while ($cat = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['categoryName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="field">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Brief description of the recipe..." required></textarea>
                </div>

                <!-- Photo -->
                <div class="field">
                    <label>Recipe Photo</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>

                <!-- Video -->
                <div class="field">
                    <label>Recipe Video (Optional)</label>
                    <input type="file" name="video" accept="video/*">
                </div>

                <!-- Ingredients -->
                <div class="field">
                    <label>Ingredients</label>
                    <div id="ingredientsContainer"></div>
                    <button type="button" class="btn btn-secondary" onclick="addIngredient()" style="margin-top:8px;">
                        + Add Ingredient
                    </button>
                </div>

                <!-- Instructions / Steps -->
                <div class="field">
                    <label>Instructions</label>
                    <div id="stepsContainer"></div>
                    <button type="button" class="btn btn-secondary" onclick="addStep()" style="margin-top:8px;">
                        + Add Step
                    </button>
                </div>

                <!-- Submit -->
                <div class="btn-row" style="justify-content:center; margin-top:20px;">
                    <button class="btn btn-primary" type="submit">Save Recipe</button>
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
    div.innerHTML = `
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
            <span style="font-size:13px; font-weight:700; min-width:90px;">Ingredient ${count}:</span>
            <input type="text" name="ingredientName[]" placeholder="e.g., flour" required style="flex:1;">
            <input type="text" name="ingredientQuantity[]" placeholder="e.g., 1 cup" required style="flex:1;">
            <button type="button" onclick="removeItem(this, 'ingredient')"
                style="background:#d8a7b1;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;">
                Remove
            </button>
        </div>
    `;
    container.appendChild(div);
}

function addStep() {
    let container = document.getElementById("stepsContainer");
    let count = container.children.length + 1;
    let div = document.createElement("div");
    div.innerHTML = `
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
            <span style="font-size:13px; font-weight:700; min-width:60px;">Step ${count}:</span>
            <input type="text" name="step[]" placeholder="Describe this step..." required style="flex:1;">
            <button type="button" onclick="removeItem(this, 'step')"
                style="background:#d8a7b1;color:white;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;">
                Remove
            </button>
        </div>
    `;
    container.appendChild(div);
}

function removeItem(button, type) {
    button.parentElement.parentElement.remove();
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

// Start with one ingredient and one step by default
window.addEventListener('DOMContentLoaded', function () {
    addIngredient();
    addStep();
});
</script>

</body>
</html>
