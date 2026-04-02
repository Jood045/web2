<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "db.php";

// التحقق من تسجيل الدخول
if (!isset($_SESSION['userID'])) {
    header("Location: login.php?error=Please login first");
    exit();
}

// التحقق أن المستخدم admin
if ($_SESSION['userType'] != "admin") {
    header("Location: login.php?error=Access denied");
    exit();
}

$adminID = $_SESSION['userID'];

// جلب بيانات الأدمن
$sqlAdmin = "SELECT * FROM users WHERE id = $adminID";
$resultAdmin = $conn->query($sqlAdmin);
$admin = $resultAdmin->fetch_assoc();

// جلب التقارير
$sqlReports = "SELECT report.id AS reportID,
                      recipe.id AS recipeID,
                      recipe.name AS recipeName,
                      users.firstName,
                      users.lastName
               FROM report
               JOIN recipe ON report.recipeID = recipe.id
               JOIN users ON recipe.userID = users.id";
$resultReports = $conn->query($sqlReports);

// جلب المستخدمين المحظورين
$sqlBlocked = "SELECT * FROM blocked_users";
$resultBlocked = $conn->query($sqlBlocked);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KidBites | Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="container header-inner">
    <a class="brand" href="index.php">
      <img src="images/logo.png" alt="KidBites Logo" class="logo">
    </a>
    <nav class="nav">
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main>
  <div class="container">

    <section class="card">
      <h2>Welcome, <?php echo $admin['firstName'] . " " . $admin['lastName']; ?> 👋</h2>
      <p class="hint">
        Admin Name: <?php echo $admin['firstName'] . " " . $admin['lastName']; ?><br>
        Email: <?php echo $admin['emailAddress']; ?>
      </p>
    </section>

    <section class="card">
      <h2>Reported Recipes</h2>

      <?php if ($resultReports && $resultReports->num_rows > 0): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Recipe Name</th>
                <th>Recipe Creator</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($report = $resultReports->fetch_assoc()): ?>
                <tr>
                  <td>
                    <a href="view_recipe.php?id=<?php echo $report['recipeID']; ?>">
                      <?php echo $report['recipeName']; ?>
                    </a>
                  </td>
                  <td>
                    <?php echo $report['firstName'] . " " . $report['lastName']; ?>
                  </td>
                  <td>
                    <form action="process_report_action.php" method="POST">
                      <input type="hidden" name="reportID" value="<?php echo $report['reportID']; ?>">
                      <input type="hidden" name="recipeID" value="<?php echo $report['recipeID']; ?>">

                      <label>
                        <input type="radio" name="action" value="block" required>
                        Block User
                      </label>

                      <label>
                        <input type="radio" name="action" value="dismiss" required>
                        Dismiss Report
                      </label>

                      <button class="btn btn-secondary btn-sm" type="submit">Submit</button>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p>No reports found.</p>
      <?php endif; ?>
    </section>

    <section class="card">
      <h2>Blocked Users</h2>

      <?php if ($resultBlocked && $resultBlocked->num_rows > 0): ?>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($blocked = $resultBlocked->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $blocked['firstName'] . " " . $blocked['lastName']; ?></td>
                  <td><?php echo $blocked['emailAddress']; ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p>No blocked users found.</p>
      <?php endif; ?>
    </section>

  </div>
</main>

<footer class="footer">
  © 2026 KidBites
</footer>

</body>
</html>