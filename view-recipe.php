<?php
session_start();
include "db.php";

// حماية الصفحة
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}





$userID = $_SESSION['userID'];
$recipeID=$_GET['id'] ?? null;
if($recipeID==null){
    die("No ID for this recipe");
}

/* =====  First Query from recipe ===== */

$sqlRecipe = "SELECT recipe.name, recipe.description, recipe.photoFileName, 
                     recipe.videoFilePath, recipe.userID,
                     recipecategory.categoryName
              FROM recipe
              JOIN recipecategory ON recipe.categoryID = recipecategory.id
              WHERE recipe.id = $recipeID";

$resultRecipe = mysqli_query($conn, $sqlRecipe);

if (!$resultRecipe) {
    die("Recipe query failed: " . mysqli_error($conn));
}

$rowRecipe = mysqli_fetch_assoc($resultRecipe);

$recipeName = $rowRecipe['name'];
$recipeDescription = $rowRecipe['description'];
$recipePhoto = $rowRecipe['photoFileName'];
$recipeVideo = $rowRecipe['videoFilePath'];
$recipeCategory = $rowRecipe['categoryName'];
$creatorID = $rowRecipe['userID'];

/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////

$hideActionButtons = false;

if ($userID == $creatorID || $_SESSION['userType'] == 'admin') {
    $hideActionButtons = true;
}
/////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////



/* =====  second Query from user (user info) ===== */

$sqlUser = "SELECT firstName, lastName, photoFileName
            FROM users
            WHERE id = $creatorID";

$resultUser = mysqli_query($conn, $sqlUser);

if (!$resultUser) {
    die("User query failed: " . mysqli_error($conn));
}

$rowUser = mysqli_fetch_assoc($resultUser);

$creatorFirstName = $rowUser['firstName'];
$creatorLastName = $rowUser['lastName'];
$creatorPhoto = $rowUser['photoFileName'];
$creatorFullName = $creatorFirstName . " " . $creatorLastName;



/* =====  third Query from ingredients  ===== */
$sqlIngredients = "SELECT ingredientName, ingredientQuantity
                   FROM ingredients
                   WHERE recipeID = $recipeID";

$resultIngredients = mysqli_query($conn, $sqlIngredients);

if (!$resultIngredients) {
    die("Ingredients query failed: " . mysqli_error($conn));
}

$ingredients = [];

while ($rowIngredient = mysqli_fetch_assoc($resultIngredients)) {
    $ingredients[] = $rowIngredient;
}

/* ===== 4th Query instructions ===== */
$sqlInstructions = "SELECT step, stepOrder
                    FROM instructions
                    WHERE recipeID = $recipeID
                    ORDER BY stepOrder ASC";

$resultInstructions = mysqli_query($conn, $sqlInstructions);

if (!$resultInstructions) {
    die("Instructions query failed: " . mysqli_error($conn));
}

$instructions = [];

while ($rowInstruction = mysqli_fetch_assoc($resultInstructions)) {
    $instructions[] = $rowInstruction;
}


/* ===== 5th Query from comment===== */
$sqlComments = "SELECT comment.comment, comment.date,
                       users.firstName, users.lastName, users.photoFileName
                FROM comment
                JOIN users ON comment.userID = users.id
                WHERE comment.recipeID = $recipeID
                ORDER BY comment.date DESC";

$resultComments = mysqli_query($conn, $sqlComments);

if (!$resultComments) {
    die("Comments query failed: " . mysqli_error($conn));
}

$comments = [];

while ($rowComment = mysqli_fetch_assoc($resultComments)) {
    $comments[] = $rowComment;
}



/* ===== 6th Query from favourites===== */
$sqlFavourite = "SELECT 1
                 FROM favourites
                 WHERE userID = $userID AND recipeID = $recipeID";

$resultFavourite = mysqli_query($conn, $sqlFavourite);

if (!$resultFavourite) {
    die("Favourite query failed: " . mysqli_error($conn));
}

$hasFavourited = (mysqli_num_rows($resultFavourite) > 0);

/* ===== 7th Query from likes===== */
$sqlLike = "SELECT *
            FROM likes
            WHERE userID = $userID AND recipeID = $recipeID";

$resultLike = mysqli_query($conn, $sqlLike);

if (!$resultLike) {
    die("Like query failed: " . mysqli_error($conn));
}

$hasLiked = (mysqli_num_rows($resultLike) > 0);


/* ===== 8th Query from report ===== */
$sqlReport = "SELECT *
              FROM report
              WHERE userID = $userID AND recipeID = $recipeID";

$resultReport = mysqli_query($conn, $sqlReport);

if (!$resultReport) {
    die("Report query failed: " . mysqli_error($conn));
}

$hasReported = (mysqli_num_rows($resultReport) > 0);


?>

<!-- HTML start -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KidBites | View Recipe</title>

  <link rel="stylesheet" href="style.css">
  
</head>

<body>
 <header>
       <div class="container header-inner">
      <a class="brand" href="index.html">
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
        <span class="badge"><?php echo $recipeCategory; ?> </span>
        <h2><?php echo $recipeName; ?></h2>
        <p class="hint"><?php $recipeDescription;?> </p>

        <div class="top-grid">
         <div class="recipe-photo">
             
             
  <img src="images/<?php echo $recipePhoto; ?>" alt="Pasta">
  
  
  
   </div>
         <?php if (!$hideActionButtons): ?>
          <div class="actions-col">
           <form action="add-favourite.php" method="post" style="margin:0;">
    <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
    <button class="action-btn <?php if ($hasFavourited) {echo 'active-favourite';} ?>"
            id="btn-favorite"
            type="submit"
            aria-label="Add recipe to favourites">
        <span class="icon-chip">♥️</span>
        <span>Add to favourites</span>
    </button>
    </form>

            <form action="add-like.php" method="post" style="margin:0;">
    <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
    <button class="action-btn <?php if ($hasLiked) { echo 'active-like'; } ?>"
            id="btn-like"
            type="submit"
            aria-label="Like recipe">
        <span class="icon-chip">👍</span>
        <span>Like recipe</span>
    </button>
</form>

           <form action="add-report.php" method="post" style="margin:0;" onsubmit="return confirmReport();">
    <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">

    <button class="action-btn <?php if ($hasReported) echo 'active-report'; ?>"
            id="btn-report"
            type="submit"
            aria-label="Report recipe"
            <?php if ($hasReported) echo "disabled"; ?>>
        <span class="icon-chip">🚩</span>
        <span><?php echo $hasReported ? 'Reported' : 'Report recipe'; ?></span>
    </button>
</form>
              
              
          </div>
            <?php endif;?>
        </div>
      </section>

      <!-- Recipe Creator -->
      <section class="card">
        <h3 style="margin-top:0;">Recipe Creator</h3>
         <div class="comment-item">
        <div class="creator-row">
          <!-- user icon replaced by IMAGE -->
          <img
            src="images/<?php echo $creatorPhoto;?> "
            alt="profile photo"
            class="avatar-img">

          <div>
            <div style="font-weight:800;"><?php echo $creatorFullName;?></div>
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
            <span class="badge" style="margin-bottom:0;"><?php echo $recipeCategory;?> </span>
          </div>

          <div>
            <div class="hint" style="margin-bottom:6px;"><strong>Description:</strong></div>
            <div class="hint" style="line-height:1.7;"> <?php echo $recipeDescription;?> </div>
          </div>
        </div>
		</div>
      </section>

      
      <!-- Ingredients -->
      <section class="card">
        <h3 style="margin-top:0;">Ingredients</h3>
		<div class="comment-item">
                    <?php if (!empty ($ingredients)):?>
        <ul class="lineHeight">
            <?php foreach($ingredients as $ingredient):?>
            <li> 
                <?php echo $ingredient['ingredientName'];?>
                <span class="badge"><?php echo  $ingredient['ingredientQuantity'];?></span>
            </li><?php endforeach;?>
        </ul>
            <?php else: ?>
            <p class ="hint"> No ingredints for this recipe </p>
            <?php endif;?>
</div>
      </section>

      <!-- Instructions -->
     <section class="card">
  <h3 style="margin-top:0;">Instructions</h3> 
  <div class="comment-item">
    <?php if (!empty($instructions)) : ?>
      <ol class="lineHeight">
        <?php foreach ($instructions as $instruction): ?>
          <li><?php echo $instruction['step']; ?></li>
        <?php endforeach; ?>
      </ol>
    <?php else : ?>
      <p class="hint">No instructions available.</p>
    <?php endif; ?>
  </div>
</section>

      <!-- Video (optional) -->
      <section class="card">
        <h3 style="margin-top:0;">Video</h3>

        
        
		<div class="comment-item">
                    <?php if(!empty($recipeVideo)): ?>
          <a class="video-link" href="<?php echo $recipeVideo;?>" target="_blank" >
            Watch on Youtube
          </a>
           <?php else : ?>
                    <p class="hint" > <strong> No video for this recipe. </strong> </p>
                    <?php endif ;?>
       
      </div>
      </section>

      <!-- Comments -->
      <section class="card">
        <h3 style="margin-top:0;">Comments</h3>

        <!--the form -->
        <form class="comment-row" id="comment-form" action="add-comment.php" method="post">
        <input type="hidden" name="recipeID" value="<?php echo $recipeID; ?>">
        <div class="field" style="margin:0;">
        <label class="label" for="comment">Add a comment</label>
       <input id="comment" name="comment" type="text" placeholder="Type your comment..." required>
       </div>
       <button class="btn btn-primary" type="submit">Add Comment</button>
       </form>

        <?php if (!empty($comments)) : ?>
     <?php foreach ($comments as $c): ?>
    <div class="comment-item">
      <div class="comment-meta">
        <img src="images/<?php echo $c['photoFileName']; ?>" 
             alt="profile photo" 
             class="avatar-img">
        <span>
          <?php echo $c['firstName'] . " " . $c['lastName']; ?>
        </span>
        
      </div>
      <div class="hint">
        <?php echo $c['comment']; ?>
      </div>
        <div class="hint badge" style="margin:5px;">
      <?php echo date("d F Y - h:i A", strtotime($c['date'])); ?>      </div>
    </div>
   <?php endforeach; ?>

   <?php else : ?>

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