<?php 
require_once '../utils/session.php';
$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}
require_once '../templates/common.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/payment.css" />
    <title>sixer - payment</title>
  </head>

  <body>
    <?php drawHeader(); ?>
    <main>
      <div class="payment-container">
        <div class="payment-header">
          <h1>Complete Your Purchase</h1>
          <p class="payment-subtitle">
            You're about to hire a John Doe for this service
          </p>
        </div>

        <div class="payment-content">
          <div class="payment-summary">
            <div class="service-card">
              <div class="service-card-image">
                <img
                  src="../assets/images/e-commerce.jpg"
                  alt="E-commerce Website Service"
                />
              </div>
              <div class="service-card-info">
                <h2>Full E-commerce Website Development</h2>
                <div class="service-card-meta">
                  <div class="freelancer-info-compact">
                    <img
                      src="../assets/images/default.jpg"
                      alt="John Doe"
                      class="freelancer-avatar-small"
                    />
                    <span class="freelancer-name">John Doe</span>
                  </div>
                  <div class="service-card-rating">
                    <span class="rating-value">5.0</span>
                    <span class="rating-stars">★★★★★</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="payment-details">
              <h2>Payment Details</h2>
              <div class="payment-breakdown">
                <div class="payment-row">
                  <span class="payment-item">Service Price</span>
                  <span class="payment-value">$499.00</span>
                </div>
                <div class="payment-row total">
                  <span class="payment-item">Total</span>
                  <span class="payment-value">$499.00</span>
                </div>
              </div>
            </div>
          </div>

          <div class="payment-form-container">
            <h2>Payment Method</h2>
            <div class="payment-method-selector">
              <div class="payment-method active">
                <span class="payment-method-name">MB WAY</span>
              </div>
            </div>

            <form class="payment-form" method="post" action="../action/hire_service.php">
              <input type="hidden" name="service_id" value="<?= htmlspecialchars($_GET['service_id'] ?? '') ?>" />
              <div class="form-group">
                <label for="phone">MB WAY - Phone Number</label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  placeholder="9xx xxx xxx"
                  pattern="[9][0-9]{8}"
                  required
                />
              </div>
              <div class="form-group">
                <label for="email">Email Address (for checkout receipt)</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="your@email.com"
                  required
                />
              </div>
              <div class="form-checkbox">
                <input type="checkbox" id="terms" name="terms" required />
                <label for="terms"
                  >I agree to the Terms of Service and Privacy Policy</label
                >
              </div>
              <button type="submit" class="pay-button">Pay $499.00</button>
              <p class="security-note">
                All transactions are secure and encrypted. By completing this
                purchase, you agree to sixer's terms and conditions.
              </p>
            </form>
          </div>
        </div>
      </div>
    </main>
  </body>
</html>
