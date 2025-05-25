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
                  <?php
                    $rating_info = Service::getServiceRatingInfo($service->id);
                  ?>
                  <span class="rating-value"><?= htmlspecialchars($rating_info['avg']) ?></span>
                  <span class="rating-count">(<?= $rating_info['count'] ?> review<?= $rating_info['count'] == 1 ? '' : 's' ?>)</span>
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
                // Show all clients with any purchases (completed or not) AND all clients that have sent a message (no repeats)
                if ($is_freelancer) {
                  $clients_from_purchases = Service::get_all_clients_for_service($service->id); // all purchases
                  $clients_from_messages = Service::get_all_message_users_for_service($service->id); // all who sent a message
                  $active_clients = array_unique(array_merge($clients_from_purchases, $clients_from_messages));
                } else {
                  $active_clients = [];
                }
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
              <?php if ($user && $service && $user['user_id'] == $service->freelancer_id): ?>
                <button class="contact-button" id="toggleForumBtn" onclick="if (!<?= $user ? 'true' : 'false' ?>) { window.location.href = 'login.php'; return false; }">Contact Clients</button>
              <?php else: ?>
                <button class="contact-button" id="toggleForumBtn" onclick="if (!<?= $user ? 'true' : 'false' ?>) { window.location.href = 'login.php'; return false; }">Contact Freelancer</button>
              <?php endif; ?>
            </div>
          </div>
        </div> <!-- end of service-header -->

        <div id="forumSection" class="service-section forum-section" style="display: none;">
          <?php if (!$user): ?>
            <p style="color:#aaa; margin:1em 0;">You must be logged in to contact a freelancer. <a href="login.php" style="color:#fff; text-decoration:underline;">Login here</a>.</p>
          <?php else: ?>
            <?php if ($is_freelancer): ?>
              <h2>Active Clients</h2>
              <?php if (empty($active_clients)): ?>
                <p class="active-clients-info">
                You have no active clients yet.<br>
                Once a client hires you or sends a message, they will appear here.
                </p>
              <?php else: ?>
                <p class="active-clients-info">
                You can chat with your clients here.<br>
                Select a client to view the conversation.
                </p>
              <?php endif; ?>
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
                    <a href="?id=<?= $service->id ?>&client_id=<?= $cid ?>&forum=open" style="text-decoration:none;">
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
                    $active_user = null;
                    if ($selected_client_id) {
                      $active_user = Database::getInstance()->query('SELECT * FROM user_registry WHERE user_id = ' . (int)$selected_client_id)->fetch();
                    }
                    echo $active_user ? htmlspecialchars($active_user['full_name']) : 'User ' . $selected_client_id;
                  ?>
                </span></h2>
                <?php
                $has_uncompleted = $is_freelancer && $selected_client_id && Service::isUserCustomer($selected_client_id, $service->id);
                $has_completed = false;
                if ($is_freelancer && $selected_client_id) {
                  $db = Database::getInstance();
                  $stmt = $db->prepare('SELECT COUNT(*) FROM purchases WHERE client_id = ? AND service_id = ? AND completed = 1');
                  $stmt->execute([$selected_client_id, $service->id]);
                  $has_completed = $stmt->fetchColumn() > 0;
                }
                if ($is_freelancer && $selected_client_id && ($has_uncompleted || $has_completed)):
                  $disabled = $has_uncompleted ? '' : 'disabled';
                  $btnText = $has_uncompleted ? 'Mark as Completed' : 'Marked as Complete';
                ?>
                  <form id="markCompletedForm" method="post" action="../action/mark_completed.php" style="display:inline; margin-left: 1em;">
                    <input type="hidden" name="service_id" value="<?= $service->id ?>" />
                    <input type="hidden" name="client_id" value="<?= $selected_client_id ?>" />
                    <button type="button" id="markCompletedBtn" class="simple-button<?= !$has_uncompleted ? ' mark-completed-disabled' : '' ?>" style="margin-left: auto;<?= !$has_uncompleted ? ' background:#333; color:#bbb; cursor:not-allowed; border:1px solid #444;' : '' ?>" <?= $disabled ?>><?= $btnText ?></button>
                  </form>
                  <script>
                    var markBtn = document.getElementById('markCompletedBtn');
                    if (markBtn && !markBtn.disabled) {
                      markBtn.addEventListener('click', function(e) {
                        if (confirm('Are you sure you want to mark this purchase as completed?')) {
                          document.getElementById('markCompletedForm').submit();
                        }
                      });
                    }
                  </script>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            <div class="forum-messages">
              <?php 
                if ($selected_client_id) {
                  $messages = Service::get_service_messages($service->id, $selected_client_id);
                  // Ensure correct client user for freelancer
                  $active_user = null;
                  if ($is_freelancer && $selected_client_id) {
                    $active_user = Database::getInstance()->query('SELECT * FROM user_registry WHERE user_id = ' . (int)$selected_client_id)->fetch();
                  }
                  foreach ($messages as $msg) {
                    drawMessage($msg, !$msg['is_reply'],
                      $is_freelancer ? ($active_user ?? $user) : $user,
                      $freelancer
                    );
                  }
                }
              ?>
            </div>
            <?php if ($user && (!$is_freelancer || ($is_freelancer && count($active_clients) > 0))): ?>
            <form class="forum-form" method="post" action="../action/send_message.php" style="margin-bottom:0;">
              <input type="hidden" name="service_id" value="<?= htmlspecialchars($service->id) ?>" />
              <?php if ($is_freelancer): ?>
                <input type="hidden" name="client_id" value="<?= $selected_client_id ?>" />
              <?php endif; ?>
              <input type="text" name="message" placeholder="Write a message..." required class="forum-input" autocomplete="off" />
              <button type="submit" aria-label="Send">
                <span class="send-text">Send</span>
                <span class="send-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                </span>
              </button>
            </form>
            <?php elseif ($is_freelancer && count($active_clients) === 0): ?>
            <form class="forum-form" style="margin-bottom:0;">
              <input type="text" name="message" placeholder="No clients available to message." class="forum-input" disabled />
              <button type="submit" aria-label="Send" disabled style="background: #333; color: #bbb; cursor: not-allowed;">
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
          <?php endif; ?>
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
                  >Here are some recent reviews from past customers</span
                >
              </div>
              <div class="reviews-stats">
                <div class="stat">
                  <span class="stat-value">
                    <?php echo Service::getTotalCustomers($service->id); ?>
                  </span>
                  <span class="stat-label">Customers</span>
                </div>
                <div class="stat">
                  <span class="stat-value">
                    <?php echo Service::getServiceRatingInfo($service->id)['count']; ?>
                  </span>
                  <span class="stat-label">Reviews</span>
                </div>
              </div>
            </div>
            <div class="reviews-list" id="reviews-list">
              <?php
                require_once __DIR__ . '/../templates/review_card.php';
                $db = Database::getInstance();
                $reviews_stmt = $db->prepare('SELECT p.*, u.full_name, u.user_picture FROM purchases p JOIN user_registry u ON p.client_id = u.user_id WHERE p.service_id = ? AND p.review_rating IS NOT NULL ORDER BY p.review_rating DESC');
                $reviews_stmt->execute([$service->id]);
                $all_reviews = $reviews_stmt->fetchAll();
                $review_count = count($all_reviews);
                $customer_count = Service::getTotalCustomers($service->id);
                $max_initial = 3;
                $show_more = $review_count > $max_initial;
                $initial_reviews = array_slice($all_reviews, 0, $max_initial);
                $remaining_reviews = array_slice($all_reviews, $max_initial);
                if ($review_count === 0 && $customer_count === 0) {
                  echo '<p style="color:#aaa; margin:0;">This service has no customers or reviews yet.</p>';
                } elseif ($review_count === 0 && $customer_count > 0) {
                  echo '<p style="color:#aaa; margin:0;">This service has customers, but no reviews have been left yet.</p>';
                }
                foreach ($initial_reviews as $review) {
                  drawReviewCard($review);
                }
                if ($show_more) {
                  echo '<div id="more-reviews" style="display:none;">';
                  foreach ($remaining_reviews as $review) {
                    drawReviewCard($review);
                  }
                  echo '</div>';
                  echo '<button id="show-more-reviews" class="submit-button" style="margin-top:1em;">Show more</button>';
                  echo '<button id="show-less-reviews" class="submit-button" style="margin-top:1em; display:none;">Show less</button>';
                }
              ?>
            </div>
            <script>
              document.addEventListener('DOMContentLoaded', function() {
                var showMoreBtn = document.getElementById('show-more-reviews');
                var showLessBtn = document.getElementById('show-less-reviews');
                var moreReviews = document.getElementById('more-reviews');
                if (showMoreBtn && showLessBtn && moreReviews) {
                  showMoreBtn.addEventListener('click', function() {
                    moreReviews.style.display = 'block';
                    showMoreBtn.style.display = 'none';
                    showLessBtn.style.display = 'inline-block';
                  });
                  showLessBtn.addEventListener('click', function() {
                    moreReviews.style.display = 'none';
                    showMoreBtn.style.display = 'inline-block';
                    showLessBtn.style.display = 'none';
                    window.scrollTo({ top: document.getElementById('reviews-list').offsetTop - 100, behavior: 'smooth' });
                  });
                }
              });
            </script>
          </div>

          <a href="profile.php?id=<?= $freelancer ? $freelancer['user_id'] : '' ?>" class="service-section">
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
            this.textContent = <?= ($user && $service && $user['user_id'] == $service->freelancer_id) ? "'Contact Clients'" : "'Contact Freelancer'"; ?>;
        }
      });
      window.addEventListener('DOMContentLoaded', function() {
        var forum = document.getElementById('forumSection');
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('forum') === 'open') {
          forum.style.display = 'block';
          var btn = document.getElementById('toggleForumBtn');
          if (btn) btn.textContent = 'Hide Forum';

          // Remove #forumSection from the URL
          if (window.location.hash === '#forumSection') {
            history.replaceState(null, '', window.location.pathname + window.location.search);
          }
        }
      });
      document.querySelectorAll('.conversation-tab').forEach(function(tab) {
        tab.addEventListener('click', function() {
          window.location.href = this.href + '&forum=open'; // Reload with forum=open
        });
      });
    </script>
  </body>
</html>
