<?php
// action/get_profile_picture.php
// Serves the user's profile picture from the database as an image

declare(strict_types=1);
require_once '../utils/database.php';
require_once '../utils/session.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$user_id) {
    header('Location: ../assets/images/default.jpg');
    exit;
}

$db = Database::getInstance();
$stmt = $db->prepare('SELECT user_picture FROM user_registry WHERE user_id = ?');
$stmt->execute([$user_id]);
$row = $stmt->fetch();

if ($row && !empty($row['user_picture'])) {
    $image_data = $row['user_picture'];
    // Always serve as JPEG since that's how we save it
    header('Content-Type: image/jpeg');
    echo $image_data;
    exit;
} else {
    header('Location: ../assets/images/default.jpg');
    exit;
}
