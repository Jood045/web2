<?php
// =====================
// 1. Session Check
// =====================
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['userType'] !== 'user') {
    header("Location: login.php?error=unauthorized");
    exit();
}

$userID = $_SESSION['userID'];

// =====================
// 2. Database Connection
// =====================
include "db.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =====================
// 3. Fetch User's Recipes with Like Count + Category Name
// =====================
$sql = "SELECT r.id, r.name, r.photoFileName, r.videoFilePath, rc.categoryName,
        (SELECT COUNT(*) FROM likes WHERE recipeID = r.id) AS likeCount
        FROM recipe r
        JOIN recipecategory rc ON r.categoryID = rc.id
        WHERE r.userID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// =====================
// 4. Fetch Ingredients & Steps per Recipe
// =====================
// We'll store them keyed by recipeID
$ingredientsMap  = [];
$instructionsMap = [];

// Re-fetch recipe IDs first so we can query ingredients/steps
$stmt2 = $conn->prepare(
    "SELECT id FROM recipe WHERE userID = ?"
);
$stmt2->bind_param("i", $userID);
$stmt2->execute();
$idsResult = $stmt2->get_result();

$recipeIDs = [];
while ($r = $idsResult->fetch_assoc()) {
    $recipeIDs[] = $r['id'];
}
$stmt2->close();

if (!empty($recipeIDs)) {
    $inList = implode(',', $recipeIDs);

    // Ingredients
    $ingResult = $conn->query(
        "SELECT recipeID, ingredientName, ingredientQuantity
         FROM ingredients
         WHERE recipeID IN ($inList)
         ORDER BY id ASC"
    );
    while ($row = $ingResult->fetch_assoc()) {
        $ingredientsMap[$row['recipeID']][] = $row;
    }

    // Instructions
    $stepResult = $conn->query(
        "SELECT recipeID, step, stepOrder
         FROM instructions
         WHERE recipeID IN ($inList)
         ORDER BY stepOrder ASC"
    );
    while ($row = $stepResult->fetch_assoc()) {
        $instructionsMap[$row['recipeID']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Recipes - KidBites</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Brief list styling inside table */
        .brief-list {
            margin: 0;
            padding-left: 16px;
            font-size: 12px;
            color: #666;
            line-height: 1.7;
        }
        .brief-list li {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        .more-label {
            font-size: 11px;
            color: #afc69a;
            font-weight: 700;
        }
        .recipe-thumb {
            width: 70px;
            height: 55px;
            border-radius: 10px;
            object-fit: cover;
            vertical-align: middle;
            margin-right: 8px;
            border: 1px solid #d9e2c9;
        }
        .recipe-name-cell {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

    <header>
        <div class="container header-inner">
            <a class="brand" href="index.php">
                <img src="images/logo.png" alt="KidBites Logo" class="logo">
            </a>
            <nav class="nav">
                <a href="user.php">Home</a>
                <a href="my-recipes.php">My Recipes</a>
                <a href="signout.php">Sign Out</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="card">
                <div class="page-header">
                    <h2>My Recipes</h2>
                    <div style="text-align:right; margin-bottom:20px;">
                        <a href="add-recipe.php" class="btn btn-primary">+ Add New Recipe</a>
                    </div>
                </div>

                <div class="table-wrap">
                    <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Recipe</th>
                                <th>Category</th>
                                <th>Ingredients</th>
                                <th>Steps</th>
                                <th>Video</th>
                                <th>Likes</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()):
                                $rid  = $row['id'];
                                $ings = $ingredientsMap[$rid]  ?? [];
                                $steps = $instructionsMap[$rid] ?? [];

                                // Show max 3 ingredients, then "+N more"
                                $maxShow = 3;
                            ?>
                            <tr>
                                <!-- Recipe: Photo + Name -->
                                <td>
                                    <a href="view-recipe.php?id=<?= $rid ?>"
                                       style="display:flex; align-items:center; gap:8px; text-decoration:none; color:inherit;">
                                        <img src="images/<?= htmlspecialchars($row['photoFileName']) ?>"
                                             alt="<?= htmlspecialchars($row['name']) ?>"
                                             class="recipe-thumb"
                                             onerror="this.style.display='none'">
                                        <span style="color:#7fa35a; font-weight:600;">
                                            <?= htmlspecialchars($row['name']) ?>
                                        </span>
                                    </a>
                                </td>

                                <!-- Category -->
                                <td><?= htmlspecialchars($row['categoryName']) ?></td>

                                <!-- Ingredients Brief -->
                                <td>
                                    <?php if (!empty($ings)): ?>
                                        <ul class="brief-list">
                                            <?php
                                            $shown = 0;
                                            foreach ($ings as $ing):
                                                if ($shown >= $maxShow) break;
                                                $shown++;
                                            ?>
                                                <li>
                                                    <?= htmlspecialchars($ing['ingredientName']) ?>
                                                    <span class="badge" style="font-size:10px; padding:2px 7px;">
                                                        <?= htmlspecialchars($ing['ingredientQuantity']) ?>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <?php if (count($ings) > $maxShow): ?>
                                            <span class="more-label">+<?= count($ings) - $maxShow ?> more</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color:#aaa; font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Steps Brief -->
                                <td>
                                    <?php if (!empty($steps)): ?>
                                        <ol class="brief-list">
                                            <?php
                                            $shown = 0;
                                            foreach ($steps as $step):
                                                if ($shown >= $maxShow) break;
                                                $shown++;
                                            ?>
                                                <li><?= htmlspecialchars($step['step']) ?></li>
                                            <?php endforeach; ?>
                                        </ol>
                                        <?php if (count($steps) > $maxShow): ?>
                                            <span class="more-label">+<?= count($steps) - $maxShow ?> more</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color:#aaa; font-size:12px;">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Video -->
                                <td>
                                    <?php if (!empty($row['videoFilePath'])): ?>
                                        <a href="videos/<?= htmlspecialchars($row['videoFilePath']) ?>"
                                           target="_blank">🎥 Watch</a>
                                    <?php else: ?>
                                        <span style="color:#aaa;">No video</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Likes -->
                                <td><?= $row['likeCount'] ?> ❤️</td>

                                <!-- Edit -->
                                <td>
                                    <a href="edit-recipe.php?id=<?= $rid ?>" class="btn btn-secondary">Edit</a>
                                </td>

                                <!-- Delete -->
                                <td>
                                    <a href="delete-recipe.php?id=<?= $rid ?>"
                                       class="btn"
                                       style="background:#d8a7b1;color:white;"
                                       onclick="return confirm('Are you sure you want to delete this recipe?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <?php else: ?>
                        <p style="text-align:center; color:#888; padding:30px;">
                            You have no recipes yet. <a href="add-recipe.php">Add your first recipe!</a>
                        </p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </main>

    <footer>
        <p>© 2026 KidBites</p>
    </footer>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
