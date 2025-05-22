<?php
require_once '../utils/database.php';
require_once '../templates/common.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare('SELECT username, email, user_picture, join_date, aboutme FROM user_registry WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header('Location: ../pages/login.php');
        exit;
    }

    $user['user_picture'] = $user['user_picture'] ?? '../assets/images/default.jpg';

} catch (PDOException $e) {
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/profile.css" />
    <title>sixer - profile</title>
  </head>

  <body>
    <?php drawHeader(); ?>
    <main>
      <div class="profile-container">
        <div class="profile-header">
          <div class="profile-avatar">
            <img
              src="<?= htmlspecialchars($user['user_picture']) ?>"
              alt="<?= htmlspecialchars($user['username']) ?>'s Profile Picture"
            />
          </div>
          <div class="profile-info">
            <div class="profile-info-top">
              <h1><?= htmlspecialchars($user['username']) ?></h1>
              <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
              <p class="profile-join-date">Member since <?= date('F Y', strtotime($user['join_date'])) ?></p>
            </div>
            <div class="profile-stats">
              <div class="stat">
                <span class="stat-value">4.8</span>
                <span class="stat-label">Rating</span>
              </div>
              <div class="stat">
                <span class="stat-value">127</span>
                <span class="stat-label">Completed Services</span>
              </div>
            </div>
          </div>
        </div>

        <div class="profile-section">
          <h2>About</h2>
            <p>
              <?php 
                if (!empty(trim($user['aboutme']))) {
                  echo htmlspecialchars($user['aboutme']);
                } else {
                  echo "User has not added a description yet.";
                }
              ?>
            </p>
        </div>

          <div class="profile-section">
            <h2>Skills</h2>
            <div class="skills-list">
              <span class="skill-tag">Web Development</span>
              <span class="skill-tag">JavaScript</span>
              <span class="skill-tag">Python</span>
              <span class="skill-tag">UI/UX Design</span>
              <span class="skill-tag">Database Design</span>
            </div>
          </div>

          <div class="profile-section">
            <h2>Your Current Services</h2>
            <div class="recent-work">
              <!-- Hardcoded example, replace with dynamic data later -->
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>Full E-commerce Website Development</h3>
                    <span class="work-date">Starting from $499</span>
                  </div>
                  <div class="work-rating">5.0 <span style="font-weight: 100; color: #999">(75)</span></div>
                </div>
                <p class="work-description">
                  I will build you a complete e-commerce website with modern UI/UX design, secure payment processing, and inventory management system.
                </p>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h2>Ongoing Purchases</h2>
            <div class="recent-work">
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>Logo Design Package</h3>
                    <span class="work-date">Paid $99 on 09/05/2025</span>
                  </div>
                  <div class="work-rating">4.1 <span style="font-weight: 100; color: #999">(105)</span></div>
                </div>
                <p class="work-description">
                  Professional logo design tailored to your brand identity. Includes 3 initial concepts and unlimited revisions.
                </p>
                <button class="review-btn" disabled>Review after delivery</button>
              </div>
            </div>
          </div>

          <div class="profile-section">
            <h2>Past Purchases</h2>
            <div class="recent-work">
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>Business Card Design</h3>
                    <span class="work-date">Paid $49 on 10/04/2025</span>
                  </div>
                  <div class="work-rating">4.7 <span style="font-weight: 100; color: #999">(12)</span></div>
                </div>
                <p class="work-description">
                  Custom business card design with print-ready files and unique branding.
                </p>

                <label class="review-label">You reviewed:</label>
                <div class="review-block">
                  <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="review-stars">★★★★★</span>
                    <span class="review-score">5.0</span>
                  </div>
                  <p class="review-text">Great work! Fast delivery and exactly what I needed.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.review-btn').forEach(function (btn) {
          btn.addEventListener('click', function () {
            var form = btn.nextElementSibling;
            if (form && form.classList.contains('review-form')) {
              form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
          });
        });
      });
    </script>
  </body>
</html>
