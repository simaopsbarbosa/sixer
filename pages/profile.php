<?php
declare(strict_types=1);
require_once '../utils/session.php';
require_once '../templates/common.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

// Determine which user's profile to show
$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $session->getUser()['user_id'];

// Fetch user from database by id
require_once '../database/user_class.php';
$user_data = null;
$db = Database::getInstance();
$stmt = $db->prepare('SELECT * FROM user_registry WHERE user_id = ?');
$stmt->execute([$profile_user_id]);
$user_data = $stmt->fetch();
if (!$user_data) {
    // User not found, redirect or show error
    echo '<main><div class="profile-container"><h2>User not found.</h2></div></main>';
    exit;
}

// For display
$user_picture = $user_data['user_picture'] ?? '../assets/images/default.jpg';
$full_name = $user_data['full_name'] ?? '';
$email = $user_data['email'] ?? '';
$join_date = $user_data['join_date'] ?? '';
$is_own_profile = ($session->getUser()['user_id'] === $profile_user_id);
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
        <div class="profile-header" style="position: relative;">
          <?php if (
            isset(
              $is_own_profile
            ) && $is_own_profile): ?>
            <a href="edit_profile.php" class="edit-profile-btn" title="Edit Profile">Edit</a>
          <?php endif; ?>
          <div class="profile-avatar" style="z-index: 1;">
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

        <div class="profile-section about-section" style="position: relative;">
          <h2>About</h2>
          <?php if ($is_own_profile): ?>
            <button id="edit-about-btn" class="edit-profile-btn">Edit</button>
          <?php endif; ?>
          <div id="aboutme-container">
            <p id="aboutme-text" style="margin-bottom:0;">
              <?php 
                $aboutme = $user_data['aboutme'] ?? null;
                if (!empty($aboutme) && trim((string)$aboutme) !== '') {
                  echo htmlspecialchars($aboutme);
                } else {
                  echo "User has not added a description yet.";
                }
              ?>
            </p>
          </div>
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
    // Review button logic (existing)
    document.querySelectorAll('.review-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var form = btn.nextElementSibling;
        if (form && form.classList.contains('review-form')) {
          form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        }
      });
    });

    // About section edit logic
    const editBtn = document.getElementById('edit-about-btn');
    const aboutContainer = document.getElementById('aboutme-container');
    const aboutText = document.getElementById('aboutme-text');
    let originalAbout = aboutText ? aboutText.textContent : '';
    if (editBtn && aboutText) {
      editBtn.addEventListener('click', function() {
        if (editBtn.textContent === 'Edit') {
          // Switch to edit mode
          aboutText.setAttribute('contenteditable', 'true');
          aboutText.style.outline = 'none';
          aboutText.style.background = 'none';
          aboutText.focus();
          editBtn.textContent = 'Save';
          editBtn.style.backgroundColor = '#ffffff'; 
          editBtn.style.color = '#000000'; 
          // Add Cancel button
          let cancelBtn = document.createElement('button');
          cancelBtn.textContent = 'Cancel';
          cancelBtn.className = 'edit-profile-btn';
          cancelBtn.style.marginRight = '80px';
          cancelBtn.id = 'cancel-about-btn';
          editBtn.parentNode.insertBefore(cancelBtn, editBtn);
          // Cancel logic
          cancelBtn.addEventListener('click', function() {
            aboutText.textContent = originalAbout;
            aboutText.removeAttribute('contenteditable');
            editBtn.textContent = 'Edit';
            editBtn.style.backgroundColor = '#111'; 
            editBtn.style.color = '#ffffff'; 
            cancelBtn.remove();
          });
        } else if (editBtn.textContent === 'Save') {
          // Save logic
          const newAbout = aboutText.textContent.trim();
          editBtn.disabled = true;
          fetch('../action/edit_profile_description.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ aboutme: newAbout })
          })
          .then(r => r.json())
          .then(data => {
            if (data.success) {
              aboutText.textContent = newAbout || 'User has not added a description yet.';
              originalAbout = aboutText.textContent;
              aboutText.removeAttribute('contenteditable');
              editBtn.textContent = 'Edit';
              editBtn.style.backgroundColor = '#111'; 
              editBtn.style.color = '#ffffff'; 
              const cancelBtn = document.getElementById('cancel-about-btn');
              if (cancelBtn) cancelBtn.remove();
            } else {
              alert('Error saving description.');
            }
          })
          .catch(() => alert('Error saving description.'))
          .finally(() => { editBtn.disabled = false; });
        }
      });
    }
  });
</script>
  </body>
</html>