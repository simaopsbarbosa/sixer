<?php
// templates/profile_service.php
// Renders a service card for the profile page
// Usage: require and call drawProfileService($service)

function drawProfileService($service) {
    // $service: instance of Service
    ?>
    <a href="../pages/service.php?id=<?= urlencode($service->id) ?>" style="text-decoration:none; color:inherit;">
    <div class="work-item">
        <div class="work-header">
            <div class="work-title-group">
                <h3><?= htmlspecialchars($service->title) ?></h3>
                <span class="work-date">Starting from $<?= number_format($service->price, 0) ?></span>
            </div>
            <div class="work-rating">
                <!-- Placeholder rating, replace with real if available -->
                5.0 <span style="font-weight: 100; color: #999">(0)</span>
            </div>
        </div>
        <p class="work-description">
            <?= nl2br(htmlspecialchars($service->info)) ?>
        </p>
    </div>
    </a>
    <?php
}
