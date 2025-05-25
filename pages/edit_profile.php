<?php
require_once '../utils/session.php';
require_once '../templates/common.php';
require_once '../utils/csrf.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = $session->getUser();
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$user_picture = $user['user_picture'] ?? '../assets/images/default.jpg';
$full_name = $user['full_name'] ?? '';
$email = $user['email'] ?? '';
$csrf_token = CSRF::getToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/auth.css" />
    <link rel="stylesheet" href="../css/edit_profile.css" />
    <link rel="icon" href="../assets/icons/favicon.ico" type="image/x-icon" />
    <title>Edit Profile</title>
</head>
<body>
<?php drawHeader(); ?>
<main class="main-background">
  <div class="auth-container">
    <div class="auth-box">
      <h2>Edit Profile</h2>
      <form class="auth-form" action="../action/edit_profile.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name) ?>" required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required />
        </div>
        <div class="form-group">
          <label for="current_password">Current Password</label>
          <input type="password" id="current_password" name="current_password" placeholder="Enter your current password to confirm changes" required />
        </div>
        <div class="form-group">
          <label for="password">New Password (Optional)</label>
          <input type="password" id="password" name="password" placeholder="Leave blank to keep current password" />
        </div>
        <div>
            <button type="submit" class="auth-button">Save Changes</button>
            <a href="#" class="simple-button" id="close-edit-profile">Close</a>
        </div>
      </form>
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error">
          <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>
      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>
    </div>
  </div>
  <script>
    const closeBtn = document.getElementById('close-edit-profile');
    if (closeBtn) {
      closeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = 'profile.php';
        }
      });
    }
  </script>
</main>
</body>
</html>
