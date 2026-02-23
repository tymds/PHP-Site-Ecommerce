<?php
declare(strict_types=1);

require_once __DIR__ . '/../private/auth.php';

start_app_session();
logout_user();

header('Location: ../login/');
exit;

