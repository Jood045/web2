<?php
session_start();
include "db.php";

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Page | KidBites</title>

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
      <a href="my-recipes.html">My Recipes</a>
      <a href="index.html">Sign Out</a>
    </nav>
  </div>
</header>

<main>
  <div class="container">
    <br><br>

<section class="card">
  <h2>Welcome, <?php echo $_SESSION['firstName']; ?></h2>

  <p class="hint">
    Name: <?php echo $_SESSION['firstName'] . " " . $_SESSION['lastName']; ?><br>
    Email: <?php echo $_SESSION['email']; ?>
  </p>

  <div>
    <img src="images/<?php echo $_SESSION['photoFileName']; ?>" width="120" style="border-radius:14px;">
  </div>
</section>

<section class="card">

<?php
$countRecipes = $conn->query("SELECT COUNT(*) as total FROM recipe WHERE userID=$userID");
$row1 = $countRecipes->fetch_assoc();

$countLikes = $conn->query("
SELECT COUNT(*) as total 
FROM likes 
JOIN recipe ON likes.recipeID = recipe.id 
WHERE recipe.userID = $userID
");
$row2 = $countLikes->fetch_assoc();
?>

<h2>
  <a href="my-recipes.html">My Recipes</a>
</h2>

<p>Total Recipes: <strong><?php echo $row1['total']; ?></strong></p>
<p>Total Likes: <strong><?php echo $row2['total']; ?></strong></p>

</section>

<section class="card">
  <h2>All Available Recipes</h2>

  <div style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
    <select>
      <option>All Categories</option>
      <option>Breakfast</option>
      <option>Lunch</option>
      <option>Dessert</option>
    </select>

    <button class="btn btn-secondary">Filter</button>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Recipe Name</th>
          <th>Recipe Photo</th>
          <th>Recipe Creator</th>
          <th>Number of Likes</th>
          <th>Category</th>
        </tr>
      </thead>

      <tbody>

<?php
$sql = "
SELECT recipe.*, users.firstName, users.lastName, users.photoFileName, recipecategory.categoryName,
(SELECT COUNT(*) FROM likes WHERE likes.recipeID = recipe.id) as likesCount
FROM recipe
JOIN users ON recipe.userID = users.id
JOIN recipecategory ON recipe.categoryID = recipecategory.id
";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){
?>

<tr>
<td>
  <a href="#"><?php echo $row['name']; ?></a>
</td>

<td>
  <img src="images/<?php echo $row['photoFileName']; ?>" width="100">
</td>

<td>
  <?php echo $row['firstName'] . " " . $row['lastName']; ?><br>
  <img src="images/<?php echo $row['photoFileName']; ?>" width="100">
</td>

<td><?php echo $row['likesCount']; ?></td>

<td><?php echo $row['categoryName']; ?></td>
</tr>

<?php } ?>

      </tbody>
    </table>
  </div>
</section>

<section class="card">
  <h2>My Favourite Recipes ♥</h2>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Recipe Name</th>
          <th>Recipe Photo</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>

<?php
$fav = "
SELECT recipe.* 
FROM favourites
JOIN recipe ON favourites.recipeID = recipe.id
WHERE favourites.userID = $userID
";

$res = $conn->query($fav);

while($row = $res->fetch_assoc()){
?>

<tr>
<td>
  <a href="#"><?php echo $row['name']; ?></a>
</td>

<td>
  <img src="images/<?php echo $row['photoFileName']; ?>" width="100">
</td>

<td>
  <a href="#">Remove</a>
</td>
</tr>

<?php } ?>

      </tbody>
    </table>
  </div>
</section>

  </div>
</main>

<footer class="footer">
  © 2026 KidBites
</footer>

</body>
</html>