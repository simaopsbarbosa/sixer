<?php
declare(strict_types=1);
require_once '../utils/session.php';
require_once '../utils/csrf.php';

$session = Session::getInstance();
$session->logout();

header('Location: ../pages/login.php');
exit;
