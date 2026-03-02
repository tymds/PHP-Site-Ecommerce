<?php
declare(strict_types=1);

$articles = [];
$authenticated = is_authenticated();
$pageTitle = 'Accueil - E-Commerce';

try {
    /** @var PDO $pdo */
    $pdo = require __DIR__ . '/../../private/db.php';
    $queries = [
        // Current schema used by account controller.
        "SELECT a.id, a.name AS nom, COALESCE(a.description, '') AS description, a.price AS prix, '' AS lien_image, u.username FROM `Article` a LEFT JOIN `User` u ON u.id = a.author_id ORDER BY a.published_at DESC",
        // Legacy schema fallback from older home implementation.
        "SELECT a.id, a.nom, COALESCE(a.description, '') AS description, a.prix, COALESCE(a.lien_image, '') AS lien_image, u.username FROM Article a JOIN User u ON a.id_auteur = u.id ORDER BY a.date_publication DESC",
    ];

    foreach ($queries as $query) {
        try {
            $stmt = $pdo->query($query);
            if ($stmt !== false) {
                $articles = $stmt->fetchAll();
                break;
            }
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
        }
    }
} catch (Throwable $exception) {
    error_log($exception->getMessage());
}

require __DIR__ . '/../views/home.php';
