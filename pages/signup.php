<?php require_once '../templates/common.php';
require_once '../utils/csrf.php';
session_start();
$csrf_token = CSRF::getToken();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/auth.css" />
    <title>sixer - sign up</title>
  </head>

  <body class="main-background">
    <?php drawHeader(); ?>
    <main>
      <div class="auth-container">
        <div class="auth-box">
          <h2>Create Account</h2>

          <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-error">
              <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
          <?php endif; ?>

          <form class="auth-form" action="../action/signup.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
            <div class="form-group">
              <label for="name">Full Name</label>
              <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                required
              />
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
              />
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Create a password"
                required
              />
            </div>
            <div class="form-group">
              <label for="confirm-password">Confirm Password</label>
              <input
                type="password"
                id="confirm-password"
                name="confirm-password"
                placeholder="Confirm your password"
                required
              />
            </div>
            <button type="submit" class="auth-button">Sign Up</button>
          </form>
          <p class="auth-switch">
            Already have an account? <a href="login.php">Login</a>
          </p>
        </div>
      </div>
    </main>
  </body>
</html>
