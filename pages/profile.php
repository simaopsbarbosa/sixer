<?php
declare(strict_types=1);
require_once '../utils/session.php';
require_once '../templates/common.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

$user = $session->getUser();
if (!$user) {
    session_destroy();
    header('Location: ../pages/login.php');
    exit;
}

$user_picture = $user['user_picture'] ?? '../assets/images/default.jpg';
$full_name = $user['full_name'] ?? '';
$email = $user['email'] ?? '';
$join_date = $user['join_date'] ?? '';
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
            <img src="<?= htmlspecialchars($user_picture) ?>" alt="<?= htmlspecialchars($full_name) ?>'s Profile Picture" />
          </div>
          <div class="profile-info">
            <div class="profile-info-top">
              <h1><?= htmlspecialchars($full_name) ?></h1>
              <p class="profile-email"><?= htmlspecialchars($email) ?></p>
              <p class="profile-join-date">Member since <?= $join_date ? date('F Y', strtotime($join_date)) : '' ?></p>
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
                $aboutme = $user['aboutme'] ?? null;
                if (!empty($aboutme) && trim((string)$aboutme) !== '') {
                  echo htmlspecialchars($aboutme);
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
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>Full E-commerce Website Development</h3>
                    <span class="work-date">Starting from $499</span>
                  </div>
                  <div class="work-rating">5.0 <span style="font-weight: 100; color: #999">(75)</span></div>
                </div>
                <p class="work-description">
                  I will build you a complete e-commerce website with modern
                  UI/UX design, secure payment processing, and inventory
                  management system.
                </p>
              </div>
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>Custom API & Backend Development</h3>
                    <span class="work-date">Starting from $299</span>
                  </div>
                  <div class="work-rating">4.8 <span style="font-weight: 100; color: #999">(52)</span></div>
                </div>
                <p class="work-description">
                  I will create a robust backend system with RESTful APIs,
                  database architecture, and complete documentation for your web
                  or mobile application.
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
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>SEO Optimization</h3>
                    <span class="work-date">Paid $150 on 05/05/2025</span>
                  </div>
                  <div class="work-rating">4.8 <span style="font-weight: 100; color: #999">(55)</span></div>
                </div>
                <p class="work-description">
                  Full website SEO audit and optimization for better search engine ranking and visibility.
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
              <div class="work-item">
                <div class="work-header">
                  <div class="work-title-group">
                    <h3>Landing Page Copywriting</h3>
                    <span class="work-date">Paid $80 on 28/03/2025</span>
                  </div>
                  <div class="work-rating">4.6 <span style="font-weight: 100; color: #999">(6)</span></div>
                </div>
                <p class="work-description">
                  Engaging and high-converting copy for your product or service landing page.
                </p>
                <button class="review-btn" type="button">Review</button>
                <form class="review-form review-form-styled" style="display:none; margin-top: 16px;" method="post">
                  <label for="review-rating" style="color:#aaa; font-size:0.9em; margin-bottom:0.5em;">Rating:</label>
                  <select id="review-rating" name="rating" required class="styled-select">
                    <option value="" disabled selected>Select rating</option>
                    <option value="5">5 (excellent)</option>
                    <option value="4">4 (good)</option>
                    <option value="3">3 (average)</option>
                    <option value="2">2 (poor)</option>
                    <option value="1">1 (terrible)</option>
                  </select>
                  <label for="review-text" style="color:#aaa; font-size:0.9em; margin-bottom:0.5em; margin-top:1em;">Review:</label>
                  <textarea id="review-text" name="review" rows="3" required class="styled-textarea"></textarea>
                  <button type="submit" class="submit-button" style="margin-top:1em;">Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.review-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var form = btn.nextElementSibling;
        if (form && form.classList.contains('review-form')) {
          form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        }
      });
    });
  });
</script>
  </body>
</html>