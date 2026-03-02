<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

class Cart {
    // Ajouter un article au panier
    public function add(int $user_id, int $article_id): void {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO Cart (user_id, article_id) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE article_id = article_id
        ");
        $stmt->execute([$user_id, $article_id]); // Exécute la requête
    }

    // Supprimer un article du panier
    public function remove(int $user_id, int $article_id): void {
        global $pdo;
        $stmt = $pdo->prepare("
            DELETE FROM Cart WHERE user_id = ? AND article_id = ?
        ");
        $stmt->execute([$user_id, $article_id]); // Exécute la suppression
    }

    // Récupérer tous les articles du panier pour un utilisateur
    public function getUserCart(int $user_id): array {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT a.id, a.name, a.price, c.quantity
            FROM Cart c
            JOIN Article a ON a.id = c.article_id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(); // Retourne tous les articles
    }
}