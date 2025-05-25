<?php
function drawProfilePurchaseCard($purchase, $service, $is_completed) {
    require_once __DIR__ . '/../database/service_class.php';
    $paid = number_format($purchase['paid_amount'] ?? $service->price, 0);
    $date = isset($purchase['purchase_date']) ? date('d/m/Y', strtotime($purchase['purchase_date'])) : '';
    $service_url = '../pages/service.php?id=' . urlencode($service->id);
    $has_review = isset($purchase['review_rating']) && $purchase['review_rating'] !== null;
    $rating_info = Service::getServiceRatingInfo($service->id);
    ?>
    <div class="work-item">
      <div class="work-header">
          <div class="work-title-group">
              <h3><a href="<?= $service_url ?>" class="service-title-link" style="text-decoration:none;color:inherit;">
                <?= htmlspecialchars($service->title) ?>
              </a></h3>
              <span class="work-date">Paid $<?= $paid ?> on <?= $date ?></span>
          </div>
          <div class="work-rating">
              <?= $rating_info['avg'] !== null ? Service::getStars((float)$rating_info['avg']) . '   ' . htmlspecialchars($rating_info['avg']) : '–' ?>
              <span style="font-weight: 100; color: #999">
                (<?= $rating_info['count'] ?>)
              </span>
          </div>
      </div>
      <p class="work-description">
          <?= nl2br(htmlspecialchars($service->info)) ?>
      </p>
      <?php if ($has_review): ?>
        <label class="review-label">You reviewed:</label>
        <div class="review-block">
          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="review-stars">
              <?= Service::getStars((float)$purchase['review_rating']) ?>
            </span>
            <span class="review-score"><?= number_format((float)$purchase['review_rating'], 1) ?></span>
          </div>
          <p class="review-text"><?= htmlspecialchars($purchase['review_text']) ?></p>
        </div>
      <?php elseif (!$is_completed): ?>
          <button class="review-btn" disabled>Review after delivery</button>
      <?php else: ?>
           <button class="review-btn" type="button">Review</button>
            <form class="review-form review-form-styled" style="display:none; margin-top: 16px;" method="post" data-purchase-id="<?= $purchase['purchase_id'] ?>">
              <label style="color:#aaa; font-size:0.9em; margin-bottom:0.5em;">Rating:</label>
              <select name="rating" required class="styled-select">
                <option value="" disabled selected>Select rating</option>
                <option value="5">5 (excellent)</option>
                <option value="4">4 (good)</option>
                <option value="3">3 (average)</option>
                <option value="2">2 (poor)</option>
                <option value="1">1 (terrible)</option>
              </select>
              <label style="color:#aaa; font-size:0.9em; margin-bottom:0.5em; margin-top:1em;">Review:</label>
              <textarea name="review" rows="3" required class="styled-textarea"></textarea>
              <button type="submit" class="submit-button" style="margin-top:1em;">Submit</button>
            </form>
      <?php endif; ?>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.review-form').forEach(function(form) {
          form.onsubmit = function(e) {
            e.preventDefault();
            const purchaseId = form.getAttribute('data-purchase-id');
            const rating = form.querySelector('[name="rating"]').value;
            const review = form.querySelector('[name="review"]').value;
            const btn = form.querySelector('.submit-button');
            btn.disabled = true;
            fetch('../action/submit_review.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ purchase_id: purchaseId, rating: rating, review: review })
            })
            .then(r => r.json())
            .then(data => {
              if (data.success) {
                location.reload();
              } else {
                alert('Failed to submit review: ' + (data.error || 'Unknown error'));
                btn.disabled = false;
              }
            })
            .catch(() => {
              alert('Failed to submit review.');
              btn.disabled = false;
            });
          };
        });
      });
    </script>
    <?php
}
