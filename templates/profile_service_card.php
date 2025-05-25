<?php

function drawProfileService($service) {
    // $service: instance of Service
    ?>
    <a href="../pages/service.php?id=<?= urlencode($service->id) ?>" style="text-decoration:none; color:inherit;">
    <div class="work-item">
        <div class="work-header">
            <div class="work-title-group">
                <h3><?= htmlspecialchars($service->title) ?></h3>
                <span class="work-date">from <span style="color:white; font-weight:bold;">$<?= number_format($service->price, 0) ?></span></span>
            </div>
            <div class="work-rating">
                <?php 
                  $rating_info = Service::getServiceRatingInfo($service->id);
                  echo Service::getStars((float)$rating_info['avg']) . ' ' . htmlspecialchars($rating_info['avg']);
                ?>
                <span style="font-weight: 100; color: #999">(<?= $rating_info['count'] ?>)</span>
            </div>
        </div>
        <p class="work-description">
            <?= nl2br(htmlspecialchars($service->info)) ?>
        </p>
    </div>
    </a>
    <?php
}
