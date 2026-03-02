<?php
declare(strict_types=1);

$articles = [];
$pageTitle = 'Accueil - E-Commerce';

try {
    /** @var PDO $pdo */
    $pdo = require __DIR__ . '/../../private/db.php';

    $query = 'SELECT a.*, u.username
              FROM `Article` a
              JOIN `User` u ON a.id_auteur = u.id
              ORDER BY a.date_publication DESC';

    $stmt = $pdo->query($query);
    if ($stmt !== false) {
        $articles = $stmt->fetchAll();
    }
} catch (Throwable $exception) {
    error_log($exception->getMessage());
}

require __DIR__ . '/../views/home.php';
