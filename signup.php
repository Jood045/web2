<?php
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>KidBites | Sign Up</title>
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
        <a href="login.php">Login</a>
      </nav>
    </div>
  </header>

  <main>
    <div class="container">

      <section class="card" style="max-width:500px; margin:auto;">
        <h2 style="text-align:center;">Create Account</h2>
        <p class="hint" style="text-align:center;">
          Please fill in your information to sign up.
        </p>

        <?php if ($error): ?>
          <p style="color:red; text-align:center;">
            <?php echo htmlspecialchars($error); ?>
          </p>
        <?php endif; ?>

        <form action="process_signup.php" method="POST" enctype="multipart/form-data">
          <div class="field">
            <label>First Name</label>
            <input type="text" name="firstName" required>
          </div>

          <div class="field">
            <label>Last Name</label>
            <input type="text" name="lastName" required>
          </div>

          <div class="field">
            <label>Email</label>
            <input type="email" name="emailAddress" required>
          </div>

          <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
          </div>

          <div class="field">
            <label>Profile Image (Optional)</label>
            <input type="file" name="photo" accept="image/*">
          </div>

          <div class="btn-row" style="justify-content:center; margin-top:16px;">
            <button class="btn btn-primary" type="submit">
              Register
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