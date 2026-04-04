<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

$userID   = $_SESSION['userID'];
$recipeID = $_GET['id'] ?? null;

if ($recipeID == null) {
    die("No ID for this recipe");
}

/* ===== 1. Recipe ===== */
$sqlRecipe = "SELECT recipe.name, recipe.description, recipe.photoFileName,
                     recipe.videoFilePath, recipe.userID,
                     recipecategory.categoryName
              FROM recipe
              JOIN recipecategory ON recipe.categoryID = recipecategory.id
              WHERE recipe.id = $recipeID";

$resultRecipe = mysqli_query($conn, $sqlRecipe);
if (!$resultRecipe) die("Recipe query failed: " . mysqli_error($conn));

$rowRecipe = mysqli_fetch_assoc($resultRecipe);

$recipeName        = $rowRecipe['name'];
$recipeDescription = $rowRecipe['description'];
$recipePhoto       = $rowRecipe['photoFileName'];    // just the filename
$recipeVideo       = $rowRecipe['videoFilePath'];    // just the filename
$recipeCategory    = $rowRecipe['categoryName'];
$creatorID         = $rowRecipe['userID'];

/* ===== Hide action buttons for owner or admin ===== */
$hideActionButtons = ($userID == $creatorID || $_SESSION['userType'] == 'admin');

/* ===== 2. Recipe Creator ===== */
$sqlUser = "SELECT firstName, lastName, photoFileName
            FROM users WHERE id = $creatorID";
$resultUser = mysqli_query($conn, $sqlUser);
if (!$resultUser) die("User query failed: " . mysqli_error($conn));

$rowUser          = mysqli_fetch_assoc($resultUser);
$creatorFirstName = $rowUser['firstName'];
$creatorLastName  = $rowUser['lastName'];
$creatorPhoto     = $rowUser['photoFileName'];
$creatorFullName  = $creatorFirstName . " " . $creatorLastName;

/* ===== 3. Ingredients ===== */
$sqlIngredients = "SELECT ingredientName, ingredientQuantity
                   FROM ingredients WHERE recipeID = $recipeID";
$resultIngredients = mysqli_query($conn, $sqlIngredients);
if (!$resultIngredients) die("Ingredients query failed: " . mysqli_error($conn));

$ingredients = [];
while ($rowIngredient = mysqli_fetch_assoc($resultIngredients)) {
    $ingredients[] = $rowIngredient;
}

/* ===== 4. Instructions ===== */
$sqlInstructions = "SELECT step, stepOrder FROM instructions
                    WHERE recipeID = $recipeID ORDER BY stepOrder ASC";
$resultInstructions = mysqli_query($conn, $sqlInstructions);
if (!$resultInstructions) die("Instructions query failed: " . mysqli_error($conn));

$instructions = [];
while ($rowInstruction = mysqli_fetch_assoc($resultInstructions)) {
    $instructions[] = $rowInstruction;
}

/* ===== 5. Comments ===== */
$sqlComments = "SELECT comment.comment, comment.date,
                       users.firstName, users.lastName, users.photoFileName
                FROM comment
                JOIN users ON comment.userID = users.id
                WHERE comment.recipeID = $recipeID
                ORDER BY comment.date DESC";
$resultComments = mysqli_query($conn, $sqlComments);
if (!$resultComments) die("Comments query failed: " . mysqli_error($conn));

$comments = [];
while ($rowComment = mysqli_fetch_assoc($resultComments)) {
    $comments[] = $rowComment;
}

/* ===== 6. Favourites ===== */
$sqlFavourite = "SELECT 1 FROM favourites
                 WHERE userID = $userID AND recipeID = $recipeID";
$resultFavourite = mysqli_query($conn, $sqlFavourite);
if (!$resultFavourite) die("Favourite query failed: " . mysqli_error($conn));
$hasFavourited = (mysqli_num_rows($resultFavourite) > 0);

/* ===== 7. Likes ===== */
$sqlLike = "SELECT * FROM likes
            WHERE userID = $userID AND recipeID = $recipeID";
$resultLike = mysqli_query($conn, $sqlLike);
if (!$resultLike) die("Like query failed: " . mysqli_error($conn));
$hasLiked = (mysqli_num_rows($resultLike) > 0);

/* ===== 8. Report ===== */
$sqlReport = "SELECT * FROM report
              WHERE userID = $userID AND recipeID = $recipeID";
$resultReport = mysqli_query($conn, $sqlReport);
if (!$resultReport) die("Report query failed: " . mysqli_error($conn));
$hasReported = (mysqli_num_rows($resultReport) > 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KidBites | <?php echo htmlspecialchars($recipeName); ?></title>
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

    <!-- TOP: name + photo + action buttons -->
    <section class="card">
      <span class="badge"><?php echo htmlspecialchars($recipeCategory); ?></span>
      <h2><?php echo htmlspecialchars($recipeName); ?></h2>
      <p class="hint"><?php echo htmlspecialchars($recipeDescription); ?></p>

      <div class="top-grid">

        <!-- Recipe Photo — stored in images/ -->
        <div class="recipe-photo">
          <?php if (!empty($recipePhoto)): ?>
            <img src="images/<?php echo htmlspecialchars($recipePhoto); ?>"
                 alt="<?php echo htmlspecialchars($recipeName); ?>">
          <?php else: ?>
            <span style="color:#aaa;">No photo</span>
          <?php endif; ?>
        </div>

        <!-- Action Buttons (hidden for owner / admin) -->
        <?php if (!$hideActionButtons): ?>
        <div class="actions-col">

          <form action="add-favourite.php" method="post" style="margin:0;">
            <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
            <button class="action-btn <?php if ($hasFavourited) echo 'active-favourite'; ?>"
                    type="submit" aria-label="Add recipe to favourites">
              <span class="icon-chip">♥️</span>
              <span>Add to favourites</span>
            </button>
          </form>

          <form action="add-like.php" method="post" style="margin:0;">
            <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
            <button class="action-btn <?php if ($hasLiked) echo 'active-like'; ?>"
                    type="submit" aria-label="Like recipe">
              <span class="icon-chip">👍</span>
              <span>Like recipe</span>
            </button>
          </form>

          <form action="add-report.php" method="post" style="margin:0;"
                onsubmit="return confirmReport();">
            <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
            <button class="action-btn <?php if ($hasReported) echo 'active-report'; ?>"
                    type="submit" aria-label="Report recipe"
                    <?php if ($hasReported) echo 'disabled'; ?>>
              <span class="icon-chip">🚩</span>
              <span><?php echo $hasReported ? 'Reported' : 'Report recipe'; ?></span>
            </button>
          </form>

        </div>
        <?php endif; ?>

      </div>
    </section>

    <!-- Recipe Creator -->
    <section class="card">
      <h3 style="margin-top:0;">Recipe Creator</h3>
      <div class="comment-item">
        <div class="creator-row">
          <!-- Creator photo stored in images/ (profile photos saved there during signup) -->
          <img src="images/<?php echo htmlspecialchars($creatorPhoto); ?>"
               alt="profile photo" class="avatar-img">
          <div>
            <div style="font-weight:800;"><?php echo htmlspecialchars($creatorFullName); ?></div>
            <div class="hint">Recipe creator</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Details -->
    <section class="card">
      <h3 style="margin-top:0;">Details</h3>
      <div class="comment-item">
        <div class="two-col">
          <div>
            <div class="hint" style="margin-bottom:6px;"><strong>Category:</strong></div>
            <span class="badge"><?php echo htmlspecialchars($recipeCategory); ?></span>
          </div>
          <div>
            <div class="hint" style="margin-bottom:6px;"><strong>Description:</strong></div>
            <div class="hint" style="line-height:1.7;">
              <?php echo htmlspecialchars($recipeDescription); ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Ingredients -->
    <section class="card">
      <h3 style="margin-top:0;">Ingredients</h3>
      <div class="comment-item">
        <?php if (!empty($ingredients)): ?>
          <ul class="lineHeight">
            <?php foreach ($ingredients as $ingredient): ?>
              <li>
                <?php echo htmlspecialchars($ingredient['ingredientName']); ?>
                <span class="badge">
                  <?php echo htmlspecialchars($ingredient['ingredientQuantity']); ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="hint">No ingredients for this recipe.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Instructions -->
    <section class="card">
      <h3 style="margin-top:0;">Instructions</h3>
      <div class="comment-item">
        <?php if (!empty($instructions)): ?>
          <ol class="lineHeight">
            <?php foreach ($instructions as $instruction): ?>
              <li><?php echo htmlspecialchars($instruction['step']); ?></li>
            <?php endforeach; ?>
          </ol>
        <?php else: ?>
          <p class="hint">No instructions available.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Video -->
    <section class="card">
      <h3 style="margin-top:0;">Video</h3>
      <div class="comment-item">
        <?php if (!empty($recipeVideo)): ?>
          <!-- Video stored in videos/ -->
          <video controls style="width:100%; border-radius:12px; max-height:360px;">
            <source src="videos/<?php echo htmlspecialchars($recipeVideo); ?>">
            Your browser does not support the video tag.
          </video>
        <?php else: ?>
          <p class="hint"><strong>No video for this recipe.</strong></p>
        <?php endif; ?>
      </div>
    </section>

    <!-- Comments -->
    <section class="card">
      <h3 style="margin-top:0;">Comments</h3>

      <form class="comment-row" action="add-comment.php" method="post">
        <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
        <div class="field" style="margin:0;">
          <label for="comment">Add a comment</label>
          <input id="comment" name="comment" type="text"
                 placeholder="Type your comment..." required>
        </div>
        <button class="btn btn-primary" type="submit">Add Comment</button>
      </form>

      <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $c): ?>
          <div class="comment-item">
            <div class="comment-meta">
              <img src="images/<?php echo htmlspecialchars($c['photoFileName']); ?>"
                   alt="profile photo" class="avatar-img">
              <span><?php echo htmlspecialchars($c['firstName'] . " " . $c['lastName']); ?></span>
            </div>
            <div class="hint"><?php echo htmlspecialchars($c['comment']); ?></div>
            <div class="hint badge" style="margin:5px;">
              <?php echo date("d F Y - h:i A", strtotime($c['date'])); ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="hint">No comments yet.</p>
      <?php endif; ?>

    </section>

  </div>
</main>

<footer class="footer">
  © 2026 KidBites
</footer>

<script>
function confirmReport() {
    return confirm("Are you sure you want to report this recipe? This action cannot be undone.");
}
</script>

</body>
</html>
