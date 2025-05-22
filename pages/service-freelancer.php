<?php require_once '../templates/common.php'; ?>
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
          <div class="service-image">
            <img
              src="../assets/images/e-commerce.jpg"
              alt="E-commerce Website Service"
            />
          </div>
          <div class="service-info">
            <div class="service-info-top">
              <h1>Full E-commerce Website Development</h1>
              <div class="service-meta">
                <span class="service-price"
                  >Starting from
                  <span style="font-weight: bold">$499</span></span
                >
                <div class="service-rating">
                  <span class="rating-value">5.0</span>
                  <span class="rating-count">(75 reviews)</span>
                </div>
              </div>
            </div>
            <div class="service-actions">
              <button class="contact-button" id="toggleForumBtn">Contact Freelancer</button>
              <a href="payment.php">
                <button class="hire-button">Hire Now</button>
              </a>
            </div>
          </div>
        </div> <!-- end of service-header -->

        <div id="forumSection" class="service-section forum-section" style="display: none;">
          <div class="conversation-tabs-container">
            <div class="conversation-tabs">
              <button class="conversation-tab active" data-user="John Doe">John Doe</button>
              <button class="conversation-tab" data-user="Sarah Johnson">Sarah Johnson</button>
              <button class="conversation-tab" data-user="Michael Chen">Michael Chen</button>
              <button class="conversation-tab" data-user="Emma Rodriguez">Emma Rodriguez</button>
            </div>
          </div>
          <div class="forum-header-row">
            <h2 style="margin: 0;">Private Forum with <span id="activeUser">John Doe</span></h2>
            <button class="simple-button" style="margin-left: auto;">Mark as Completed</button>
          </div>
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
              <span
                >Delivery in
                <span class="service-eta">2 weeks</span>
              </span>
            </div>
            <p>
              I will build you a complete e-commerce website with modern UI/UX
              design, secure payment processing, and inventory management
              system. The website will be fully responsive, optimized for all
              devices, and include all essential features for a successful
              online store.
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

          <a href="profile.php" class="service-section">
            <h2>About The Freelancer</h2>
            <div class="freelancer-info">
              <div class="freelancer-header">
                <img
                  src="../assets/images/default.jpg"
                  alt="John Doe"
                  class="freelancer-avatar"
                />
                <div class="freelancer-details">
                  <h3>John Doe</h3>
                  <p class="freelancer-location">Portugal</p>
                </div>
              </div>
              <p class="freelancer-description">
                Experienced freelancer specializing in web development and
                programming. Based in Portugal.
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
          this.textContent = 'View Conversations';
        }
      });

      // Tab switching logic
      const tabs = document.querySelectorAll('.conversation-tab');
      const activeUserSpan = document.getElementById('activeUser');
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          document.querySelector('.conversation-tab.active').classList.remove('active');
          this.classList.add('active');
          activeUserSpan.textContent = this.dataset.user;
          // In a real app, here you would load the conversation for the selected user
        });
      });
    </script>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;400;700&display=swap');

      
    </style>
  </body>
</html>
