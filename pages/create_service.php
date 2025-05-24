<?php
require_once '../utils/session.php';
require_once '../templates/common.php';
require_once '../database/service_class.php';
require_once '../utils/csrf.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $session->getUser();
    $freelancer_id = $user['user_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $price = $_POST['price'] ?? '';
    $delivery_time = $_POST['delivery_time'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');

    $errors = [];
    if (!$freelancer_id) $errors[] = 'User not found.';
    if ($title === '') $errors[] = 'Title is required.';
    if ($price === '' || !is_numeric($price) || $price <= 0) $errors[] = 'Valid price is required.';
    if ($delivery_time === '' || !is_numeric($delivery_time) || $delivery_time <= 0) $errors[] = 'Valid delivery time is required.';
    if ($description === '') $errors[] = 'Description is required.';
    if ($category === '') $errors[] = 'Category is required.';
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) $errors[] = 'Image is required.';

    if (count($errors) === 0) {
        $image_blob = file_get_contents($_FILES['image']['tmp_name']);
        $info = $description;
        $eta = $delivery_time;
        $service_id = Service::create($freelancer_id, $title, $price, $info, $eta, $category, $image_blob);
        
        header('Location: service.php?id=' . $service_id);
        exit;
    } else {
        
        echo '<script>alert("' . implode('\\n', $errors) . '"); window.history.back();</script>';
        exit;
    }
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
    <title>sixer - create service</title>
  </head>
  <body>
    <?php drawHeader(); ?>
    <main>
      <div class="service-container">
        <form class="service-form" action="#" method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>" />
          <h1>Create a New Service</h1>
          <h2 class="service-subtitle">
            <span>You will be able to access your new service on your profile,<br>under <span style="font-weight:bold;">Current Services</span>.</span>
          </h2>
          <div class="service-header">
            <div class="service-image-upload service-image-upload-picker" style="aspect-ratio: 16/9; position: relative; overflow: hidden; cursor: pointer;">
              <input type="file" id="service-image" name="image" accept="image/*" style="display:none;" />
              <div id="service-image-preview" class="service-image-preview" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#18181b;border:1px dashed #444;aspect-ratio:16/9;position:relative;">
                <span id="service-image-placeholder" style="color:#bbb;font-size:1.2em;pointer-events:none;transition:opacity 0.2s;">Select Image</span>
                <div id="service-image-hover" style="display:none;position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.32);color:#fff;align-items:center;justify-content:center;font-size:1.1em;z-index:2;pointer-events:none;transition:opacity 0.2s;">Change Image</div>
              </div>
            </div>
            <div class="service-main-fields">
              <div class="service-title">
                <label for="service-title">Title</label>
                <input type="text" class="input-field" id="service-title" name="title" maxlength="100" required />
              </div>
              <div class="service-fields-row">
                <div class="service-field">
                  <label for="service-price">Base Price ($)</label>
                  <input type="number" class="input-field" id="service-price" name="price" min="1" step="0.01" />
                </div>
                <div class="service-field">
                  <label for="service-delivery">Base Delivery Time (days)</label>
                  <input type="number" class="input-field" id="service-delivery" name="delivery_time" min="1"/>
                </div>
              </div>
            </div>
          </div>
          <div class="service-section about-service-section">
            <label for="service-description">Description</label>
            <div class="service-section-description">Describe your service in detail so buyers know exactly what to expect.</div>
            <textarea id="service-description" name="description" rows="6" maxlength="1000" required></textarea>
          </div>
          <div class="service-section">
            <label for="service-category">Category</label>
            <div class="service-section-description">Choose the category that best fits your service.</div>
            <select id="service-category" name="category" required>
              <option value="">Select a category</option>
              <option value="e-commerce">E-commerce</option>
              <option value="design">Design</option>
              <option value="writing">Writing</option>
              <option value="translation">Translation</option>
              <option value="programming">Programming</option>
              <option value="marketing">Marketing</option>
              <option value="video">Video & Animation</option>
              <option value="music">Music & Audio</option>
              <option value="business">Business</option>
              <option value="lifestyle">Lifestyle</option>
              <option value="other">Other</option>
            </select>
          </div>
        <button type="submit" class="hire-button">Create Service</button>
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
