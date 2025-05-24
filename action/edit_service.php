<?php
require_once '../database/service_class.php';
require_once '../utils/session.php';

$session = Session::getInstance();
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
    // Optionally handle image update here if needed
    Service::update_service($service_id, $title, $category, $price, $eta, $info);
    header('Location: ../pages/service.php?id=' . $service_id);
    exit();
  }
}
