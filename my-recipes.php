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
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Recipes - Lunchbox Legends</title>
    <link rel="stylesheet" href="style.css">
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
                                <th>Video</th>
                                <th>Likes</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <!-- Recipe: Photo + Name as link to view recipe -->
                                <td>
                                    <a href="view-recipe.php?id=<?= $row['id'] ?>">
                                        <img src="uploads/recipes/<?= htmlspecialchars($row['photoFileName']) ?>"
                                             alt="<?= htmlspecialchars($row['name']) ?>"
                                             style="width:80px;height:60px;border-radius:10px;margin-right:10px;vertical-align:middle;">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </a>
                                </td>

                                <!-- Category -->
                                <td><?= htmlspecialchars($row['categoryName']) ?></td>

                                <!-- Video -->
                                <td>
                                    <?php if (!empty($row['videoFilePath'])): ?>
                                        <a href="uploads/videos/<?= htmlspecialchars($row['videoFilePath']) ?>" target="_blank">🎥 Watch Video</a>
                                    <?php else: ?>
                                        <span style="color:#aaa;">No video</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Likes -->
                                <td><?= $row['likeCount'] ?> ❤️</td>

                                <!-- Edit -->
                                <td>
                                    <a href="edit-recipe.php?id=<?= $row['id'] ?>" class="btn btn-secondary">Edit</a>
                                </td>

                                <!-- Delete -->
                                <td>
                                    <a href="delete-recipe.php?id=<?= $row['id'] ?>"
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