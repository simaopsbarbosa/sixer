<?php 
require_once '../templates/common.php';
require_once '../database/service_class.php';
require_once '../database/user_class.php';
require_once '../templates/message.php';

// Get service id from query string
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$service = $service_id ? Service::get_by_id($service_id) : null;

$freelancer = null;
if ($service) {
  $db = Database::getInstance();
  $stmt = $db->prepare('SELECT * FROM user_registry WHERE user_id = ?');
  $stmt->execute([$service->freelancer_id]);
  $freelancer = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/service.css" />
    <title>sixer - service</title>
  </head>

  <body>
    <?php drawHeader(); ?>
    <main>
      <div class="service-container">
        <div class="service-header">
          <div class="service-image" style="aspect-ratio: 16/9; width: 100%; background: #18181b; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <?php if ($service && $service->picture): ?>
              <img
                src="data:image/jpeg;base64,<?= base64_encode($service->picture) ?>"
                alt="Service Image"
                style="width: 100%; height: 100%; object-fit: cover; aspect-ratio: 16/9;"
              />
            <?php else: ?>
              <img
                src="../assets/images/e-commerce.jpg"
                alt="E-commerce Website Service"
                style="width: 100%; height: 100%; object-fit: cover; aspect-ratio: 16/9;"
              />
            <?php endif; ?>
          </div>
          <div class="service-info">
            <div class="service-info-top">
              <h1><?= htmlspecialchars($service ? $service->title : 'Service Not Found') ?></h1>
              <div class="service-meta">
                <span class="service-category">
                  <?= $service ? htmlspecialchars($service->category) : '-' ?>
                </span>
                <span class="service-price"
                  >Starting from
                  <span style="font-weight: bold">
                    <?= htmlspecialchars($service ? $service->price : '-') ?>$
                  </span></span
                >
                <div class="service-rating">
                  <span class="rating-value">5.0</span>
                  <span class="rating-count">(75 reviews)</span>
                </div>
              </div>
            </div>
            <div class="service-actions">
              <?php 
                require_once '../utils/session.php';
                $session = Session::getInstance();
                $user = $session->getUser();

                // Determine conversation context
                $is_freelancer = $user && $service && $user['user_id'] == $service->freelancer_id;
                // Update: allow any user to send a message to a freelancer
                $active_clients = $is_freelancer ? Service::get_all_message_users_for_service($service->id) : [];
                $selected_client_id = null;
                if ($is_freelancer) {
                    if (isset($_GET['client_id']) && in_array((int)$_GET['client_id'], $active_clients)) {
                        $selected_client_id = (int)$_GET['client_id'];
                    } elseif (!empty($active_clients)) {
                        $selected_client_id = (int)$active_clients[0];
                    }
                } else {
                    $selected_client_id = $user ? $user['user_id'] : null;
                }
              ?>
              <?php if ($user && $service && $user['user_id'] == $service->freelancer_id): ?>
                <a href="edit_service.php?id=<?= $service->id ?>">
                  <button class="hire-button" type="button">Edit</button>
                </a>
              <?php elseif ($user && $service && Service::isUserCustomer($user['user_id'], $service->id)): ?>
                <div><button class="hire-button" type="button" disabled style="background: #333; color: #bbb; cursor: not-allowed;">Hired</button></div>
              <?php else: ?>
                <a href="payment.php?service_id=<?= $service->id ?>">
                  <button class="hire-button">Hire Now</button>
                </a>
              <?php endif; ?>
              <button class="contact-button" id="toggleForumBtn">Contact Freelancer</button>
            </div>
          </div>
        </div> <!-- end of service-header -->

        <div id="forumSection" class="service-section forum-section" style="display: none;">
          <?php if ($is_freelancer): ?>
            <h2>Active Clients</h2>
            <p class="active-clients-info">
              You can chat with your active clients here.<br>
              Select a client to view the conversation.
            </p>
            <?php else: ?>
            <h2>Private Messages with Freelancer</h2>
            <?php endif; ?>
          <?php if ($is_freelancer && count($active_clients) > 0): ?>
            <div class="conversation-tabs-container">
              <div class="conversation-tabs">
                <?php foreach ($active_clients as $i => $cid):
                  $cuser = $cid ? (Database::getInstance()->prepare('SELECT * FROM user_registry WHERE user_id = ?')->execute([$cid]) ? Database::getInstance()->prepare('SELECT * FROM user_registry WHERE user_id = ?')->execute([$cid]) : null) : null;
                  $cuser = $cuser ? Database::getInstance()->query('SELECT * FROM user_registry WHERE user_id = ' . (int)$cid)->fetch() : null;
                  $active = $selected_client_id == $cid;
                ?>
                  <a href="?id=<?= $service->id ?>&client_id=<?= $cid ?>#forumSection" style="text-decoration:none;">
                    <button class="conversation-tab<?= $active ? ' active' : '' ?>" data-user="<?= $cuser ? htmlspecialchars($cuser['full_name']) : 'User ' . $cid ?>">
                      <?= $cuser ? htmlspecialchars($cuser['full_name']) : 'User ' . $cid ?>
                    </button>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="forum-header-row">
              <h2>Private Messages with <span id="activeUser">
                <?php
                  $active_cuser = null;
                  if ($selected_client_id) {
                    $active_cuser = Database::getInstance()->query('SELECT * FROM user_registry WHERE user_id = ' . (int)$selected_client_id)->fetch();
                  }
                  echo $active_cuser ? htmlspecialchars($active_cuser['full_name']) : 'User ' . $selected_client_id;
                ?>
              </span></h2>
              <!-- Optionally, add Mark as Completed button here if needed -->
            </div>
          <?php endif; ?>
          <div class="forum-messages">
            <?php 
              if ($selected_client_id) {
                $messages = Service::get_service_messages($service->id, $selected_client_id);
                foreach ($messages as $msg) {
                  drawMessage($msg, !$msg['is_reply'],
                    $is_freelancer ? ($cuser ?? $user) : $user,
                    $freelancer
                  );
                }
              }
            ?>
          </div>
          <?php if ($user): ?>
          <form class="forum-form" method="post" style="margin-bottom:0;">
            <input type="text" name="message" placeholder="Write a message..." required class="forum-input" autocomplete="off" />
            <input type="hidden" name="send_message" value="1" />
            <button type="submit" aria-label="Send">
              <span class="send-text">Send</span>
              <span class="send-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
              </span>
            </button>
          </form>
          <?php endif; ?>
          <label class="forum-notify-label">
            <input type="checkbox" name="notify_email" />
            Send email nofitication for new messages
          </label>
        </div>

        <div class="service-content">
          <div class="service-section about-service-section">
            <div class="about-service-header">
              <h2>About This Service</h2>
              <span>
                Delivery in
                <span class="service-eta">
                  <?= $service ? ($service->eta . ' day' . ($service->eta == 1 ? '' : 's')) : '-' ?>
                </span>
              </span>
            </div>
            <p>
              <?= $service ? nl2br(htmlspecialchars($service->info)) : 'Service not found.' ?>
            </p>
          </div>

          <div class="service-section">
            <div class="section-header">
              <div class="reviews-title">
                <h2>Reviews</h2>
                <span class="reviews-subtitle"
                  >Here are some recent reviews from satisfied customers</span
                >
              </div>
              <div class="reviews-stats">
                <div class="stat">
                  <span class="stat-value">127</span>
                  <span class="stat-label">Customers</span>
                </div>
                <div class="stat">
                  <span class="stat-value">75</span>
                  <span class="stat-label">Reviews</span>
                </div>
              </div>
            </div>
            <div class="reviews-list">
              <div class="review-card">
                <div class="review-header">
                  <div class="reviewer-info">
                    <img
                      src="../assets/images/default.jpg"
                      alt="Sarah Johnson"
                      class="reviewer-avatar"
                    />
                    <div class="reviewer-details">
                      <span class="reviewer-name">Sarah Johnson</span>
                      <span class="review-date">2 weeks ago</span>
                    </div>
                  </div>
                  <div class="review-rating">5.0 ★★★★★</div>
                </div>
                <p class="review-text">
                  "Absolutely amazing work! The e-commerce site was delivered on
                  time and exceeded my expectations. The payment integration was
                  seamless and the admin dashboard is very intuitive."
                </p>
              </div>

              <div class="review-card">
                <div class="review-header">
                  <div class="reviewer-info">
                    <img
                      src="../assets/images/default.jpg"
                      alt="Michael Chen"
                      class="reviewer-avatar"
                    />
                    <div class="reviewer-details">
                      <span class="reviewer-name">Michael Chen</span>
                      <span class="review-date">1 month ago</span>
                    </div>
                  </div>
                  <div class="review-rating">5.0 ★★★★★</div>
                </div>
                <p class="review-text">
                  "Professional and responsive throughout the entire process.
                  The inventory management system works perfectly and the site
                  loads incredibly fast. Highly recommend!"
                </p>
              </div>

              <div class="review-card">
                <div class="review-header">
                  <div class="reviewer-info">
                    <img
                      src="../assets/images/default.jpg"
                      alt="Emma Rodriguez"
                      class="reviewer-avatar"
                    />
                    <div class="reviewer-details">
                      <span class="reviewer-name">Emma Rodriguez</span>
                      <span class="review-date">2 months ago</span>
                    </div>
                  </div>
                  <div class="review-rating">4.8 ★★★★☆</div>
                </div>
                <p class="review-text">
                  "Great experience working with this freelancer. The site looks
                  modern and professional. Only minor issues with the initial
                  setup, but they were resolved quickly."
                </p>
              </div>
            </div>
          </div>

          <a href="profile.php?id=<?= $freelancer ? htmlspecialchars($freelancer['user_id']) : '' ?>" class="service-section">
            <h2>About The Freelancer</h2>
            <div class="freelancer-info">
              <div class="freelancer-header">
                <img
                  src="<?= $freelancer && !empty($freelancer['user_picture']) ? '../action/get_profile_picture.php?id=' . $freelancer['user_id'] : '../assets/images/default.jpg' ?>"
                  alt="<?= htmlspecialchars($freelancer['full_name'] ?? 'Freelancer') ?>"
                  class="freelancer-avatar"
                />
                <div class="freelancer-details">
                  <h3><?= htmlspecialchars($freelancer['full_name'] ?? 'Unknown') ?></h3>
                  <p class="freelancer-email"><?= htmlspecialchars($freelancer['email'] ?? '-') ?></p>
                </div>
              </div>
              <p class="freelancer-description">
                <?= htmlspecialchars($freelancer['aboutme'] ?? 'No description provided.') ?>
              </p>
            </div>
          </a>
        </div>
      </div>
    </main>
    <script>
      document.getElementById('toggleForumBtn').addEventListener('click', function() {
        var forum = document.getElementById('forumSection');
        if (forum.style.display === 'none') {
          forum.style.display = 'block';
          this.textContent = 'Hide Forum';
        } else {
          forum.style.display = 'none';
          this.textContent = 'Contact Freelancer';
        }
      });
      // Open forum by default if hash is #forumSection or after sending
      window.addEventListener('DOMContentLoaded', function() {
        var forum = document.getElementById('forumSection');
        if (window.location.hash === '#forumSection' || window.performance && performance.navigation.type === 1) {
          forum.style.display = 'block';
          var btn = document.getElementById('toggleForumBtn');
          if (btn) btn.textContent = 'Hide Forum';
        }
      });
      // Submit forum form with AJAX for instant update
      var forumForm = document.querySelector('.forum-form');
      if (forumForm) {
        forumForm.addEventListener('submit', function(e) {
          e.preventDefault();
          var formData = new FormData(forumForm);
          fetch(window.location.pathname + window.location.search, {
            method: 'POST',
            body: formData
          })
          .then(function() { window.location.hash = '#forumSection'; window.location.reload(); });
        });
      }
    </script>
  </body>
</html>

<?php
// Handle message sending
if (
    isset($_POST['send_message'], $_POST['message']) &&
    $user && $service && $selected_client_id && trim($_POST['message']) !== ''
) {
    $msg_text = trim($_POST['message']);
    $is_reply = $is_freelancer ? 1 : 0;
    $msg_user_id = $is_freelancer ? $selected_client_id : $user['user_id'];
    $db = Database::getInstance();
    $stmt = $db->prepare('INSERT INTO messages (service_id, user_id, message_text, is_reply, date_time) VALUES (?, ?, ?, ?, datetime("now"))');
    $stmt->execute([$service->id, $msg_user_id, $msg_text, $is_reply]);
    echo '<script>window.location.hash = "#forumSection"; window.location.reload();</script>';
    exit;
}
