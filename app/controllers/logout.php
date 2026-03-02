<?php
declare(strict_types=1);

if (!is_post_request()) {
    redirect_to('/home');
}

if (!is_valid_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
    redirect_to('/home');
}

logout_user();
redirect_to('/login');
