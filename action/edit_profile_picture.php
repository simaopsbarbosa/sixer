<?php
// action/edit_profile_picture.php
// Handles profile picture upload and cropping

declare(strict_types=1);
require_once '../utils/session.php';
require_once '../utils/database.php';
require_once '../utils/csrf.php';


$session = Session::getInstance();

$csrf_token = $_POST['csrf_token'] ?? '';
if (!CSRF::verifyCSRF($csrf_token)) {
http_response_code(403);
echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
exit;
}

if (!$session->isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

$user_id = $session->getUser()['user_id'] ?? null;
if (!$user_id) {
    header('Location: ../pages/profile.php');
    exit;
}

if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    header('Location: ../pages/profile.php?error=upload');
    exit;
}

$tmp_name = $_FILES['profile_picture']['tmp_name'];
$img_info = getimagesize($tmp_name);
if (!$img_info) {
    header('Location: ../pages/profile.php?error=invalidimg');
    exit;
}

$ext = image_type_to_extension($img_info[2]);
$allowed = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
if (!in_array(strtolower($ext), $allowed)) {
    header('Location: ../pages/profile.php?error=type');
    exit;
}

// Crop to 1:1 (center square)
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
    default:
        header('Location: ../pages/profile.php?error=type');
        exit;
}
$w = imagesx($src_img);
$h = imagesy($src_img);
$size = min($w, $h);
$x = (int)(($w - $size) / 2);
$y = (int)(($h - $size) / 2);
$dst_img = imagecreatetruecolor($size, $size);
imagecopyresampled($dst_img, $src_img, 0, 0, $x, $y, $size, $size, $size, $size);

// Convert cropped image to binary data
ob_start();
imagejpeg($dst_img, null, 92);
$image_data = ob_get_clean();
imagedestroy($src_img);
imagedestroy($dst_img);

// Update DB with image blob
$db = Database::getInstance();
$stmt = $db->prepare('UPDATE user_registry SET user_picture = ? WHERE user_id = ?');
$stmt->bindParam(1, $image_data, PDO::PARAM_LOB);
$stmt->bindParam(2, $user_id, PDO::PARAM_INT);
$stmt->execute();

header('Location: ../pages/profile.php');
exit;
