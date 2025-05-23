<?php
require_once '../utils/session.php';
require_once '../templates/common.php';

$session = Session::getInstance();
if (!$session->isLoggedIn()) {
    header('Location: login.php');
    exit;
}
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
          <h1>Create a New Service</h1>
          <h2 class="service-subtitle">
            <span>You will be able to access your new service on your profile,<br>under <span style="font-weight:bold;">Current Services</span>.</span>
          </h2>
          <div class="service-header efficient-layout">
            <div class="service-image-upload">
              <span style="margin-bottom:1em;">Select Image</span>
              <input type="file" id="service-image" name="image" accept="image/*" required style="cursor:pointer;" />
            </div>
            <div class="service-main-fields">
              <div class="service-title">
                <label for="service-title">Title</label>
                <input type="text" id="service-title" name="title" maxlength="100" required />
              </div>
              <div class="service-fields-row">
                <div class="service-field">
                  <label for="service-price">Base Price ($)</label>
                  <input type="number" id="service-price" name="price" min="1" step="0.01" required />
                </div>
                <div class="service-field">
                  <label for="service-delivery">Base Delivery Time (days)</label>
                  <input type="number" id="service-delivery" name="delivery_time" min="1" max="60" required />
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
  </body>
</html>
