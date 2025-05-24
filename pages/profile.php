<?php
declare(strict_types=1);
require_once '../utils/session.php';
require_once '../templates/common.php';
require_once '../database/user_class.php';
require_once '../database/service_class.php';
require_once '../templates/profile_service.php';
require_once '../templates/profile_purchase_card.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

// Determine which user's profile to show
$profile_user_id = isset($_GET['id']) ? intval($_GET['id']) : $session->getUser()['user_id'];

// Fetch user from database by id
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

$user_services = Service::get_by_freelancer($profile_user_id);

$user_skills = [];
$stmt = $db->prepare('SELECT skill_name FROM user_skills WHERE user_id = ?');
$stmt->execute([$profile_user_id]);
$user_skills = $stmt->fetchAll(PDO::FETCH_COLUMN);

$all_skills = [];
$stmt = $db->prepare('SELECT skill_name FROM skills ORDER BY skill_name ASC');
$stmt->execute();
$all_skills = $stmt->fetchAll(PDO::FETCH_COLUMN);

function hasAnyServices($user_services) {
    return !empty($user_services) && count($user_services) > 0;
}

// Fetch user purchases
$user_purchases = User::get_user_purchases($profile_user_id);

// Split purchases into ongoing and past
$ongoing_purchases = [];
$past_purchases = [];
foreach ($user_purchases as $purchase) {
    if (!empty($purchase['service_id'])) {
        $service = Service::get_by_id($purchase['service_id']);
        if ($service) {
            if ($purchase['completed']) {
                $past_purchases[] = ['purchase' => $purchase, 'service' => $service];
            } else {
                $ongoing_purchases[] = ['purchase' => $purchase, 'service' => $service];
            }
        }
    }
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
              Edit
            </button>
            <button id="edit_skills-btn" class="edit-profile-btn" type="button" style="display: <?= empty($user_skills) ? 'none' : 'inline-block' ?>;">
              Edit
            </button>
          <?php endif; ?>
          <div id="skills-container" class="skills-container">
            <span id="skills-text">
              <?php 
                if (empty($user_skills)) {
                  echo "No skills have been added yet.";
                } else {
                  echo '<div class="skills-list">';
                  foreach ($user_skills as $skill) {
                    echo '<span class="skill-tag">' . htmlspecialchars($skill) . '</span> ';
                  }
                  echo '</div>';
                }
              ?>
            </span>
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
          
          <?php if (hasAnyServices($user_services)): ?>
            <div class="profile-section">
              <h2>Your Current Services</h2>
              <div class="recent-work">
                <?php 
                  foreach ($user_services as $service) {
                    drawProfileService($service);
                  }
                ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="profile-section">
            <h2>Ongoing Purchases</h2>
            <div class="recent-work">
              <?php if (!empty($ongoing_purchases)) {
                foreach ($ongoing_purchases as $item) {
                  drawProfilePurchaseCard($item['purchase'], $item['service'], false);
                }
              } else {
                echo '<p style="color:#888;">No ongoing purchases.</p>';
              } ?>
            </div>
          </div>

          <div class="profile-section">
            <h2>Past Purchases</h2>
            <div class="recent-work">
              <?php if (!empty($past_purchases)) {
                foreach ($past_purchases as $item) {
                  drawProfilePurchaseCard($item['purchase'], $item['service'], true);
                }
              } else {
                echo '<p style="color:#888;">No past purchases.</p>';
              } ?>
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

    const editBtn = document.getElementById('edit-about-btn');
    const aboutContainer = document.getElementById('aboutme-container');
    const aboutText = document.getElementById('aboutme-text');
    let originalAbout = aboutText ? aboutText.textContent : '';
    if (editBtn && aboutText) {
      editBtn.addEventListener('click', function() {
        if (editBtn.textContent === 'Edit') {
           
          aboutText.setAttribute('contenteditable', 'true');
          aboutText.style.outline = 'none';
          aboutText.style.background = 'none';
          aboutText.focus();
          editBtn.textContent = 'Save';
          editBtn.style.backgroundColor = '#ffffff'; 
          editBtn.style.color = '#000000'; 
           
          let cancelBtn = document.createElement('button');
          cancelBtn.textContent = 'Cancel';
          cancelBtn.className = 'edit-profile-btn';
          cancelBtn.style.marginRight = '80px';
          cancelBtn.id = 'cancel-about-btn';
          editBtn.parentNode.insertBefore(cancelBtn, editBtn);
           
          cancelBtn.addEventListener('click', function() {
            aboutText.textContent = originalAbout;
            aboutText.removeAttribute('contenteditable');
            editBtn.textContent = 'Edit';
            editBtn.style.backgroundColor = '#111'; 
            editBtn.style.color = '#ffffff'; 
            cancelBtn.remove();
          });
        } else if (editBtn.textContent === 'Save') {
           
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

    const addSkillBtn = document.getElementById('add_skills-btn');
    const editSkillsBtn = document.getElementById('edit_skills-btn');
    const skillsDropdown = document.getElementById('skills_dropdown');
    const skillsText = document.getElementById('skills-text');
    let originalSkills = skillsText ? skillsText.innerHTML : '';
    let originalChecked = [];

    function getCheckedSkills() {
      return Array.from(skillsDropdown.querySelectorAll('input[type="checkbox"]:checked')).map(cb => cb.value);
    }

    function setCheckedSkills(skills) {
      skillsDropdown.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = skills.includes(cb.value);
      });
    }

    function enterEditSkillsMode() {

      originalChecked = getCheckedSkills();
      skillsDropdown.style.display = 'block';
      if (addSkillBtn) addSkillBtn.style.display = 'none';
      if (editSkillsBtn) editSkillsBtn.style.display = 'none';

      let cancelBtn = document.createElement('button');
      cancelBtn.textContent = 'Cancel';
      cancelBtn.className = 'edit-profile-btn';
      cancelBtn.id = 'cancel-skills-btn';
      cancelBtn.style.marginRight = '80px';

      let saveBtn = document.createElement('button');
      saveBtn.textContent = 'Save';
      saveBtn.className = 'edit-profile-btn';
      saveBtn.id = 'save-skills-btn';
      saveBtn.style.background = '#fff';
      saveBtn.style.color = '#000';

      skillsDropdown.parentNode.insertBefore(cancelBtn, skillsDropdown);
      skillsDropdown.parentNode.insertBefore(saveBtn, skillsDropdown);

      cancelBtn.addEventListener('click', function(e) {
        e.preventDefault();
        setCheckedSkills(originalChecked);
        skillsDropdown.style.display = 'none';
        cancelBtn.remove();
        saveBtn.remove();
        if (addSkillBtn) addSkillBtn.style.display = originalSkills.includes('No skills') ? 'inline-block' : 'none';
        if (editSkillsBtn) editSkillsBtn.style.display = originalSkills.includes('No skills') ? 'none' : 'inline-block';
      });

      saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const checked = getCheckedSkills();
        fetch('../action/edit_profile_skills.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ skills: checked })
        })
        .then r => r.json())
        .then(data => {
          if (data.success) {
            skillsText.innerHTML = data.skills.length
              ? '<div class="skills-list">' + data.skills.map(skill => `<span class=\"skill-tag\">${skill}</span>`).join(' ') + '</div>'
              : 'No skills have been added yet.';
            originalSkills = skillsText.innerHTML;
            setCheckedSkills(data.skills);
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

<!-- for future reference - this is how to draw a purchase card with review:
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
  </div> -->

