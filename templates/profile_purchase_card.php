<?php
// templates/profile_purchase_card.php
// Renders a purchase card for the profile page
// Usage: require and call drawProfilePurchaseCard($purchase, $service, $is_completed)

function drawProfilePurchaseCard($purchase, $service, $is_completed) {
    // $purchase: array with purchase info (including paid amount, date, etc)
    // $service: instance of Service
    // $is_completed: bool
    $paid = number_format($purchase['paid_amount'] ?? $service->price, 0);
    $date = isset($purchase['purchase_date']) ? date('d/m/Y', strtotime($purchase['purchase_date'])) : '';
    ?>
    <div class="work-item">
        <div class="work-header">
            <div class="work-title-group">
                <h3><?= htmlspecialchars($service->title) ?></h3>
                <span class="work-date">Paid $<?= $paid ?> on <?= $date ?></span>
            </div>
            <div class="work-rating">
                <!-- Placeholder rating, replace with real if available -->
                4.8 <span style="font-weight: 100; color: #999">(0)</span>
            </div>
        </div>
        <p class="work-description">
            <?= nl2br(htmlspecialchars($service->info)) ?>
        </p>
        <?php if (!$is_completed): ?>
            <button class="review-btn" disabled>Review after delivery</button>
        <?php else: ?>
            <button class="review-btn" type="button">Review</button>
        <?php endif; ?>
    </div>
    <?php
}
