<?php
declare(strict_types=1);
require_once(__DIR__ . '/../utils/session.php');

$session = Session::getInstance();
$session->logout();

header('Location: ../pages/login.php');
exit;
