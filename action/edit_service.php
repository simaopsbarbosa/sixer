<?php
require_once '../database/service_class.php';
require_once '../utils/session.php';
require_once '../utils/csrf.php';

$session = Session::getInstance();

$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

$user_id = $session->getUser()['user_id'] ?? null;

$service_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$service = $service_id ? Service::get_by_id($service_id) : null;

if (!$service || $service->freelancer_id !== $user_id) {
  header('Location: ../pages/profile.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete'])) {
    // Delete service
    Service::delete_by_id($service_id);
    header('Location: ../pages/profile.php');
    exit();
  } else {
    // Update service
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $eta = intval($_POST['delivery_time']); // use delivery_time from form
    $info = trim($_POST['description']); // use description from form
    
    // Handle image update if a new image is provided
    $image_blob = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $tmp_name = $_FILES['image']['tmp_name'];
      $image_blob = file_get_contents($tmp_name);
      
      // Process the image - for consistency with profile picture processing
      $img_info = getimagesize($tmp_name);
      if ($img_info) {
        // Create image resource based on file type
        $src_img = null;
        switch ($img_info[2]) {
          case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($tmp_name);
            break;
          case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($tmp_name);
            break;
          case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($tmp_name);
            break;
          case IMAGETYPE_WEBP:
            $src_img = imagecreatefromwebp($tmp_name);
            break;
        }
        
        if ($src_img) {
          // Simply optimize the image for storage
          ob_start();
          imagejpeg($src_img, null, 92);
          $image_blob = ob_get_clean();
          imagedestroy($src_img);
        }
      }
    }
    
    Service::update_service($service_id, $title, $category, $price, $eta, $info, $image_blob);
    header('Location: ../pages/service.php?id=' . $service_id);
    exit();
  }
}
