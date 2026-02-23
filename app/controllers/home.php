<?php
declare(strict_types=1);

$authenticated = is_authenticated();
$username = $authenticated ? (string)($_SESSION['username'] ?? '') : '';
$email = $authenticated ? (string)($_SESSION['email'] ?? '') : '';
$pageTitle = 'Shop | Home';

require __DIR__ . '/../views/home.php';
