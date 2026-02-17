<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

start_app_session();
logout_user();

header('Location: ../login/');
exit;

