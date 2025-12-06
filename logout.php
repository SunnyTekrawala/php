<?php
require_once 'includes/autoloader.php';

$db = new Database();
$authManager = new AuthManager($db);

$authManager->logout();

header('Location: index.php');
exit;
