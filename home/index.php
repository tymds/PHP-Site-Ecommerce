<?php
declare(strict_types=1);

require_once __DIR__ . '/../private/auth.php';

start_app_session();

$authenticated = is_authenticated();

$username = $authenticated ? (string)($_SESSION['username'] ?? '') : '';
$email = $authenticated ? (string)($_SESSION['email'] ?? '') : '';

require __DIR__ . '/view.php';
