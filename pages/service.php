<?php 
require_once '../templates/common.php';
require_once '../database/service_class.php';
require_once '../database/user_class.php';

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
          <h2>Private Forum with Freelancer</h2>
          <div class="forum-messages">
            <div class="forum-message">
              <img src="../assets/images/default.jpg" alt="John Doe" class="forum-avatar" />
              <div class="forum-message-content">
                <div class="forum-message-header">
                  <span class="forum-username">John Doe</span>
                  <span class="forum-date">2025-05-20</span>
                </div>
                <div class="forum-text">Hello! How can I help you with your e-commerce project?</div>
              </div>
            </div>
            <div class="forum-message">
              <img src="../assets/images/default.jpg" alt="You" class="forum-avatar" />
              <div class="forum-message-content">
                <div class="forum-message-header">
                  <span class="forum-username">You</span>
                  <span class="forum-date">2025-05-21</span>
                </div>
                <div class="forum-text">Hi! I have some questions about payment integration.</div>
              </div>
            </div>
          </div>
          <form class="forum-form" method="post">
            <input type="text" name="message" placeholder="Write a message..." required class="forum-input" />
            <button type="submit" aria-label="Send">
              <span class="send-text">Send</span>
              <span class="send-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
              </span>
            </button>
          </form>
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
    </script>
  </body>
</html>
