<?php
declare(strict_types=1);

if (!is_authenticated()) {
    redirect_to('/login');
}

$errors = [];
$pageTitle = 'Vendre un article - E-Commerce';

require __DIR__ . '/../views/sell_view.php';
