<?php require_once '../templates/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/auth.css" />
    <title>sixer - login</title>
  </head>

  <body class="main-background">
    <?php drawHeader(); ?>
    <main>
      <div class="auth-container">
        <div class="auth-box">
          <h2>Welcome Back</h2>
          <form class="auth-form" id="loginForm" method="POST" action="../action/login.php">
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
                placeholder="Enter your password"
                required
              />
            </div>
            <button type="submit" class="auth-button">Login</button>
          </form>
          <p class="auth-switch">
            Don't have an account? <a href="signup.php">Sign up</a>
          </p>
        </div>
      </div>
    </main>
  </body>
</html>
