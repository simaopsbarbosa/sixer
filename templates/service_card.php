<?php
require_once '../templates/common.php';
require_once '../database/service_class.php';

function get_user_full_name($user_id) {
    $db = Database::getInstance();
    $stmt = $db->prepare('SELECT full_name FROM user_registry WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $row = $stmt->fetch();
    return $row ? $row['full_name'] : '';
}

function drawServiceCard($service_id) {
    $service = Service::get_by_id($service_id);
    if (!$service) return;
    $freelancer_name = htmlspecialchars(get_user_full_name($service->freelancer_id));
    $desc = $service->info;
    $desc_lines = explode("\n", $desc);
    $description = $desc_lines[0];
    // use default image if none
    $img_src = '../assets/images/assembly.jpg'; // fallback
    if (!empty($service->picture)) {
        $img_src = 'data:image/jpeg;base64,' . base64_encode($service->picture);
    }
    ?>
    <a href="service.php?id=<?= htmlspecialchars($service->id) ?>" class="search-card">
        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($service->title) ?>" />
        <div class="search-card-content">
            <div class="search-card-text">
                <span class="search-card-title">
                    <?= $freelancer_name ?>
                </span><br />
                <span class="search-card-desc">
                    <?= htmlspecialchars($service->title) ?>
                </span>
            </div>
            <div class="search-card-stats">
                <div class="search-card-info">
                    <span class="search-card-delivery">
  <?php
    $eta_display = ($service->eta > 999) ? '999+' : htmlspecialchars($service->eta);
  ?>
  <?= $eta_display ?> day<?= $service->eta == 1 ? '' : 's' ?>
</span>
                    <span class="search-card-rating">
                      <?php
                        $rating_info = Service::getServiceRatingInfo($service->id);
                        echo Service::getStars((float)$rating_info['avg']) . ' ' . htmlspecialchars($rating_info['avg']);
                      ?>
                    </span>
                </div>
                <span class="search-card-price">from <span class="search-card-price-bold"><?= htmlspecialchars($service->price) ?>$</span></span>
            </div>
        </div>
    </a>
    <?php
}
