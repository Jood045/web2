<?php
session_start();
include "db.php";

// حماية الصفحة
if (!isset($_SESSION['userID']) || $_SESSION['userType'] != 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];


// ===== بيانات المستخدم =====
$userQuery = "SELECT * FROM users WHERE id = $userID";
$userResult = $conn->query($userQuery);
$user = $userResult->fetch_assoc();


// ===== عدد الوصفات =====
$countRecipes = "SELECT COUNT(*) as total FROM recipe WHERE userID = $userID";
$res1 = $conn->query($countRecipes);
$totalRecipes = $res1->fetch_assoc()['total'];


// ===== عدد اللايكات =====
$countLikes = "
SELECT COUNT(*) as totalLikes 
FROM likes 
JOIN recipe ON recipe.id = likes.recipeID
WHERE recipe.userID = $userID
";
$res2 = $conn->query($countLikes);
$totalLikes = $res2->fetch_assoc()['totalLikes'];


// ===== جلب الكاتقوري =====
$categories = $conn->query("SELECT * FROM recipecategory");


// ===== فلترة =====
$where = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['categoryID']) && $_POST['categoryID'] != "") {
    $catID = $_POST['categoryID'];
    $where = "WHERE recipe.categoryID = $catID";
}


// ===== كل الريسيبي =====
$recipesQuery = "
SELECT recipe.*, 
       users.firstName, users.lastName, users.photoFileName AS userPhoto,
       recipecategory.categoryName,
       COUNT(likes.recipeID) as totalLikes
FROM recipe
JOIN users ON recipe.userID = users.id
JOIN recipecategory ON recipe.categoryID = recipecategory.id
LEFT JOIN likes ON recipe.id = likes.recipeID
$where
GROUP BY recipe.id
";
$recipes = $conn->query($recipesQuery);


// ===== الفافوريت =====
$favQuery = "
SELECT recipe.*
FROM favourites
JOIN recipe ON favourites.recipeID = recipe.id
WHERE favourites.userID = $userID
";
$favourites = $conn->query($favQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Page | KidBites</title>

<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
<div class="container header-inner">
<a class="brand" href="index.php">
<img src="images/logo.png" class="logo">
</a>

<nav class="nav">
<a href="user.php">User page</a>
<a href="my-recipes.php">My Recipes</a>
<a href="logout.php">Sign Out</a>
</nav>
</div>
</header>

<main>
<div class="container">

<br><br>

<!-- ===== USER INFO ===== -->
<section class="card">
<h2>Welcome, <?= $user['firstName'] ?></h2>

<p class="hint">
Name: <?= $user['firstName']." ".$user['lastName'] ?><br>
Email: <?= $user['emailAddress'] ?>
</p>

<div>
<img src="images/<?= $user['photoFileName'] ?>" width="120" style="border-radius:14px;">
</div>
</section>


<!-- ===== SUMMARY ===== -->
<section class="card">
<h2><a href="my-recipes.html">My Recipes</a></h2>

<p>Total Recipes: <strong><?= $totalRecipes ?></strong></p>
<p>Total Likes: <strong><?= $totalLikes ?></strong></p>
</section>


<!-- ===== ALL RECIPES ===== -->
<section class="card">
<h2>All Available Recipes</h2>

<form method="POST">
<div style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">

<select name="categoryID">
<option value="">All Categories</option>

<?php while($cat = $categories->fetch_assoc()): ?>
<option value="<?= $cat['id'] ?>"
<?= (isset($_POST['categoryID']) && $_POST['categoryID'] == $cat['id']) ? 'selected' : '' ?>>
<?= $cat['categoryName'] ?>
</option>
<?php endwhile; ?>

</select>

<button class="btn btn-secondary">Filter</button>
</div>
</form>


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

<?php if($recipes->num_rows > 0): ?>

<?php while($row = $recipes->fetch_assoc()): ?>

<tr>

<td>
<a href="view-recipe.php?id=<?= $row['id'] ?>">
<?= $row['name'] ?>
</a>
</td>

<td>
<img src="images/<?= $row['photoFileName'] ?>" width="100">
</td>

<td>
<?= $row['firstName']." ".$row['lastName'] ?><br>

<img src="images/<?= $row['userPhoto'] ?>" width="100" style="margin-top:5px;">
</td>

<td>
<?= $row['totalLikes'] ?>
</td>

<td>
<?= $row['categoryName'] ?>
</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="5" class="empty-msg">No recipes found</td>
</tr>

<?php endif; ?>

</tbody>

</table>
</div>
</section>


<!-- ===== FAVOURITES ===== -->
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

<?php if($favourites->num_rows > 0): ?>
<?php while($row = $favourites->fetch_assoc()): ?>

<tr>

<td>
<a href="view-recipe.php?id=<?= $row['id'] ?>">
<?= $row['name'] ?>
</a>
</td>

<td>
<img src="images/<?= $row['photoFileName'] ?>" width="100">
</td>

<td>
<a href="remove_fav.php?id=<?= $row['id'] ?>" class="btn btn-danger">
Remove</a>
</td>

</tr>

<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="3" class="empty-msg">No favourites yet</td>
</tr>
<?php endif; ?>

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