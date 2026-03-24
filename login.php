<?php
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KidBites | Login</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header>
    <div class="container header-inner">
      <a class="brand" href="index.php">
        <img src="images/logo.png" alt="KidBites Logo" class="logo">
      </a>

      <nav class="nav">
        <a href="index.php">Home</a>
        <a href="signup.php">Sign Up</a>
      </nav>
    </div>
  </header>

  <main>
    <div class="container">

      <section class="card" style="max-width:500px; margin:auto;">
        <h2 style="text-align:center;">Login</h2>
        <p class="hint" style="text-align:center;">
          Please enter your email and password.
        </p>

        <?php if ($error): ?>
          <p style="color:red; text-align:center;">
            <?php echo htmlspecialchars($error); ?>
          </p>
        <?php endif; ?>

        <form action="process_login.php" method="POST">

          <div class="field">
            <label>Email</label>
            <input type="email" name="emailAddress" required>
          </div>

          <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
          </div>

          <div class="btn-row" style="justify-content:center; margin-top:16px;">
            <button class="btn btn-primary" type="submit">
              Login
            </button>
          </div>
        </form>

      </section>

    </div>
  </main>

  <footer class="footer">
    © 2026 KidBites
  </footer>

</body>
</html>