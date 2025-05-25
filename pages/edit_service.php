<?php
require_once '../templates/common.php';
require_once '../database/service_class.php';
require_once '../utils/session.php';
require_once '../utils/csrf.php';

$service_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$service = $service_id ? Service::get_by_id($service_id) : null;

$session = Session::getInstance();
$user = $session->getUser();

if (!$service) {
  header('Location: profile.php');
  exit();
}

if ($service->freelancer_id !== ($user['user_id'] ?? null)) {
  header('Location: profile.php');
  exit();
}
$csrf_token = CSRF::getToken();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/styles.css" />
    <link rel="stylesheet" href="../css/service.css" />
    <link rel="stylesheet" href="../css/create_service.css" />
    <style>
      html, body, input, select, textarea, button {
        font-family: 'Inter', sans-serif !important;
      }
    </style>
    <title>sixer - edit service</title>
  </head>
  <body>
    <?php drawHeader(); ?>
    <main>
      <div class="service-container">
        <form class="service-form" action="../action/edit_service.php?id=<?= $service->id ?>" method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
          <h1>Edit Service</h1>
          <h2 class="service-subtitle">
            <span>You are editing your service.<br>Changes will be visible on your profile and service page.</span>
          </h2>
          <div class="service-header">
            <div class="service-image-upload service-image-upload-picker" style="aspect-ratio: 16/9; position: relative; overflow: hidden; cursor: pointer;">
              <input type="file" id="service-image" name="image" accept="image/*" style="display:none;" />
              <div id="service-image-preview" class="service-image-preview" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#18181b;border:1px dashed #444;aspect-ratio:16/9;position:relative;">
                <?php if ($service->picture): ?>
                  <img src="data:image/jpeg;base64,<?= base64_encode($service->picture) ?>" alt="Service Image" style="max-width:100%;max-height:100%;object-fit:cover;" />
                  <span id="service-image-placeholder" style="display:none;">Select Image</span>
                <?php else: ?>
                  <span id="service-image-placeholder" style="color:#bbb;font-size:1.2em;pointer-events:none;transition:opacity 0.2s;">Select Image</span>
                <?php endif; ?>
                <div id="service-image-hover" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.32);color:#fff;align-items:center;justify-content:center;font-size:1.1em;z-index:2;pointer-events:none;transition:opacity 0.2s;">Change Image</div>
              </div>
            </div>
            <div class="service-main-fields">
              <div class="service-title">
                <label for="service-title">Title</label>
                <input type="text" class="input-field" id="service-title" name="title" maxlength="100" required value="<?= htmlspecialchars($service->title) ?>" />
              </div>
              <div class="service-fields-row">
                <div class="service-field">
                  <label for="service-price">Base Price ($)</label>
                  <input type="number" class="input-field" id="service-price" name="price" min="1" step="0.01" value="<?= htmlspecialchars($service->price) ?>" />
                </div>
                <div class="service-field">
                  <label for="service-delivery">Base Delivery Time (days)</label>
                  <input type="number" class="input-field" id="service-delivery" name="delivery_time" min="1" value="<?= htmlspecialchars($service->eta) ?>" />
                </div>
              </div>
            </div>
          </div>
          <div class="service-section about-service-section">
            <label for="service-description">Description</label>
            <div class="service-section-description">Describe your service in detail so buyers know exactly what to expect.</div>
            <textarea id="service-description" name="description" rows="6" maxlength="1000" required><?= htmlspecialchars($service->info) ?></textarea>
          </div>
          <div class="service-section">
            <label for="service-category">Category</label>
            <div class="service-section-description">Choose the category that best fits your service.</div>
            <select id="service-category" name="category" required>
              <option value="">Select a category</option>
              <?php
                $db = Database::getInstance();
                $stmt = $db->query('SELECT category_name FROM categories ORDER BY category_name');
                $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
                foreach ($categories as $cat) {
                  $selected = ($service->category === $cat) ? 'selected' : '';
                  echo '<option value="' . htmlspecialchars($cat) . '" ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                }
              ?>
            </select>
          </div>
          <div class="form-actions" style="margin-top: 2em; display: flex; gap: 1em;">
            <a style="flex-grow: 1" href="service.php?id=<?= $service->id ?>" class="hire-button cancel-button">Cancel</a>
            <button style="flex-grow: 2" type="submit" class="hire-button">Save Changes</button>
            <button style="flex-grow: 1" type="submit" name="delete" value="1" class="hire-button delete-button" onclick="return confirm('Are you sure you want to delete this service? This action cannot be undone.');">Delete Service</button>
          </div>
        </form>
      </div>
    </main>
    <script>
document.addEventListener('DOMContentLoaded', function() {
  const fileInput = document.getElementById('service-image');
  const preview = document.getElementById('service-image-preview');
  const placeholder = document.getElementById('service-image-placeholder');
  const hoverOverlay = document.getElementById('service-image-hover');

  preview.addEventListener('click', function(e) {
    e.stopPropagation();
    fileInput.click();
  });
  if (placeholder) {
    placeholder.addEventListener('click', function(e) {
      e.stopPropagation();
      fileInput.click();
    });
  }

  preview.addEventListener('mouseenter', function() {
    const img = preview.querySelector('img');
    if (img) {
      hoverOverlay.style.display = 'flex';
      hoverOverlay.style.opacity = '1';
    } else {
      hoverOverlay.style.display = 'none';
      hoverOverlay.style.opacity = '0';
    }
  });
  preview.addEventListener('mouseleave', function() {
    hoverOverlay.style.opacity = '0';
    setTimeout(() => { hoverOverlay.style.display = 'none'; }, 200);
  });

  fileInput.addEventListener('change', function() {
    if (fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        let img = preview.querySelector('img');
        if (!img) {
          img = document.createElement('img');
          preview.appendChild(img);
        }
        img.src = e.target.result;
        img.style.display = 'block';
        placeholder.style.display = 'none';
      };
      reader.readAsDataURL(fileInput.files[0]);
    } else {
      const img = preview.querySelector('img');
      if (img) img.remove();
      placeholder.style.display = 'block';
      hoverOverlay.style.display = 'none';
      hoverOverlay.style.opacity = '0';
    }
  });
});
</script>
  </body>
</html>
