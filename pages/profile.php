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
if (!empty($user_data['user_picture'])) {
    // Serve image via a separate endpoint
    $user_picture = '../action/get_profile_picture.php?id=' . $profile_user_id;
} else {
    $user_picture = '../assets/images/default.jpg';
}
$full_name = $user_data['full_name'] ?? '';
$email = $user_data['email'] ?? '';
$join_date = $user_data['join_date'] ?? '';
$is_own_profile = ($session->getUser()['user_id'] === $profile_user_id);

$user_skills = [];
$stmt = $db->prepare('SELECT skill_name FROM user_skills WHERE user_id = ?');
$stmt->execute([$profile_user_id]);
$user_skills = $stmt->fetchAll(PDO::FETCH_COLUMN);

$all_skills = [];
$stmt = $db->prepare('SELECT skill_name FROM skills ORDER BY skill_name ASC');
$stmt->execute();
$all_skills = $stmt->fetchAll(PDO::FETCH_COLUMN);
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
          <div class="profile-avatar" style="z-index: 1; position: relative; cursor: pointer;">
            <form id="profile-pic-form" action="../action/edit_profile_picture.php" method="post" enctype="multipart/form-data" style="display:none;">
              <input type="file" id="profile-pic-input" name="profile_picture" accept="image/*" style="display:none;" />
            </form>
            <img id="profile-pic-img" src="<?= htmlspecialchars($user_picture) ?>" alt="<?= htmlspecialchars($full_name) ?>'s Profile Picture" style="object-fit: cover; aspect-ratio: 1/1; width: 100%; height: 100%; border-radius: 0; cursor: pointer;" />
            <?php if ($is_own_profile): ?>
              <div id="profile-pic-overlay" style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.25); color:#fff; display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity 0.2s; border-radius:0; pointer-events:none; font-size:1.1em;">Change</div>
            <?php endif; ?>
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

        <div class="profile-section" style="position: relative;">
          <h2>Skills</h2>
          <?php if ($is_own_profile): ?>
            <button id="add_skills-btn" class="edit-profile-btn" style="display: <?= empty($user_skills) ? 'inline-block' : 'none' ?>;">
              Add +
            </button>
            <button id="edit_skills-btn" class="edit-profile-btn" type="button" style="display: <?= empty($user_skills) ? 'none' : 'inline-block' ?>;">
              Edit
            </button>
          <?php endif; ?>
          <div id="skills-container">
            <p id="skills-text" style="margin-bottom:0;">
              <?php 
                if (empty($user_skills)) {
                  echo "No skills have been added yet.";
                } else {
                  foreach ($user_skills as $skill) {
                    echo '<span class="skill-tag">' . htmlspecialchars($skill) . '</span> ';
                  }
                }
              ?>
            </p>

            <form id="skills_dropdown" class="skills-dropdown" style="display:none;">
              <?php foreach ($all_skills as $skill): ?>
                <label style="display:block; margin-bottom:4px;">
                  <input type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>"
                    <?= in_array($skill, $user_skills) ? 'checked' : '' ?>>
                  <?= htmlspecialchars($skill) ?>
                </label>
              <?php endforeach; ?>
            </form>
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

    // Profile picture change logic
    const profilePicImg = document.getElementById('profile-pic-img');
    const profilePicInput = document.getElementById('profile-pic-input');
    const profilePicForm = document.getElementById('profile-pic-form');
    const profilePicOverlay = document.getElementById('profile-pic-overlay');
    <?php if ($is_own_profile): ?>
    if (profilePicImg && profilePicInput && profilePicForm) {
      profilePicImg.addEventListener('mouseenter', function() {
        if (profilePicOverlay) profilePicOverlay.style.opacity = 1;
      });
      profilePicImg.addEventListener('mouseleave', function() {
        if (profilePicOverlay) profilePicOverlay.style.opacity = 0;
      });
      profilePicImg.addEventListener('click', function() {
        profilePicInput.click();
      });
      if (profilePicOverlay) {
        profilePicOverlay.addEventListener('mouseenter', function() {
          profilePicOverlay.style.opacity = 1;
        });
        profilePicOverlay.addEventListener('mouseleave', function() {
          profilePicOverlay.style.opacity = 0;
        });
        profilePicOverlay.addEventListener('click', function() {
          profilePicInput.click();
        });
      }
      profilePicInput.addEventListener('change', function() {
        if (profilePicInput.files && profilePicInput.files[0]) {
          profilePicForm.submit();
        }
      });
    }
    <?php endif; ?>

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

    // Skills section edit logic
    const addSkillBtn = document.getElementById('add_skills-btn');
    const editSkillsBtn = document.getElementById('edit_skills-btn');
    const skillsDropdown = document.getElementById('skills_dropdown');
    const skillsText = document.getElementById('skills-text');
    let originalSkills = skillsText ? skillsText.innerHTML : '';

    function enterEditSkillsMode() {
      // Show dropdown
      skillsDropdown.style.display = 'block';
      if (addSkillBtn) addSkillBtn.style.display = 'none';
      if (editSkillsBtn) editSkillsBtn.style.display = 'none';

      // Create Cancel button
      let cancelBtn = document.createElement('button');
      cancelBtn.textContent = 'Cancel';
      cancelBtn.className = 'edit-profile-btn';
      cancelBtn.id = 'cancel-skills-btn';
      cancelBtn.style.marginRight = '80px';

      // Create Save button
      let saveBtn = document.createElement('button');
      saveBtn.textContent = 'Save';
      saveBtn.className = 'edit-profile-btn';
      saveBtn.id = 'save-skills-btn';
      saveBtn.style.background = '#fff';
      saveBtn.style.color = '#000';

      // Insert buttons before dropdown
      skillsDropdown.parentNode.insertBefore(cancelBtn, skillsDropdown);
      skillsDropdown.parentNode.insertBefore(saveBtn, skillsDropdown);

      // Cancel logic
      cancelBtn.addEventListener('click', function() {
        skillsDropdown.style.display = 'none';
        cancelBtn.remove();
        saveBtn.remove();
        skillsText.innerHTML = originalSkills;
        if (skillsText.textContent.trim() === 'No skills have been added yet.') {
          if (addSkillBtn) addSkillBtn.style.display = 'inline-block';
          if (editSkillsBtn) editSkillsBtn.style.display = 'none';
        } else {
          if (addSkillBtn) addSkillBtn.style.display = 'none';
          if (editSkillsBtn) editSkillsBtn.style.display = 'inline-block';
        }
      });

      // Save logic
      saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const checked = Array.from(skillsDropdown.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value);
        fetch('../action/edit_profile_skills.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ skills: checked })
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            skillsText.innerHTML = data.skills.length
              ? data.skills.map(skill => `<span class="skill-tag">${skill}</span>`).join(' ')
              : 'No skills have been added yet.';
            if (addSkillBtn) addSkillBtn.style.display = data.skills.length ? 'none' : 'inline-block';
            if (editSkillsBtn) editSkillsBtn.style.display = data.skills.length ? 'inline-block' : 'none';
            skillsDropdown.style.display = 'none';
            cancelBtn.remove();
            saveBtn.remove();
          } else {
            alert('Error saving skills.');
          }
        })
        .catch(() => alert('Error saving skills.'));
      });
    }
    
    if (addSkillBtn) addSkillBtn.addEventListener('click', enterEditSkillsMode);
    if (editSkillsBtn) editSkillsBtn.addEventListener('click', enterEditSkillsMode);
  });

</script>
  </body>
</html>