<?php
function drawReviewCard($review) {
    require_once __DIR__ . '/../database/service_class.php';
    $reviewer_name = htmlspecialchars($review['full_name'] ?? 'User');
    $review_date = $review['purchase_date'] ? date('F j, Y', strtotime($review['purchase_date'])) : '';
    $review_rating = (float)($review['review_rating'] ?? 0);
    $review_text = htmlspecialchars($review['review_text'] ?? '');
    $reviewer_picture = !empty($review['user_picture'])
        ? '../action/get_profile_picture.php?id=' . $review['client_id']
        : '../assets/images/default.jpg';
    ?>
    <div class="review-card">
        <div class="review-header">
            <div class="reviewer-info">
                <img src="<?= $reviewer_picture ?>" alt="<?= $reviewer_name ?>" class="reviewer-avatar" />
                <div class="reviewer-details">
                    <span class="reviewer-name"><?= $reviewer_name ?></span>
                    <span class="review-date"><?= $review_date ?></span>
                </div>
            </div>
            <div class="review-rating">
                <?= number_format($review_rating, 1) ?> <?= Service::getStars($review_rating) ?>
            </div>
        </div>
        <p class="review-text">
            "<?= $review_text ?>"
        </p>
    </div>
    <?php
}
