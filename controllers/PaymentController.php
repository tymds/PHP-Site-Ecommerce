<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../config/database.php';

start_app_session();
$user_id = $_SESSION['user_id'] ?? null;
$cart = new Cart();

try {
    $pdo->beginTransaction(); // Commence une transaction SQL

    // Récupère les articles du panier avec le stock
    $stmt = $pdo->prepare("
        SELECT a.id, a.price, c.quantity, s.quantity AS stock
        FROM Cart c
        JOIN Article a ON a.id = c.article_id
        JOIN Stock s ON s.article_id = a.id
        WHERE c.user_id = ?
        FOR UPDATE
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();

    if (!$items) {
        throw new Exception("Panier vide."); // Stop si panier vide
    }

    // Calcul du total et vérification du stock
    $total = 0;
    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock']) {
            throw new Exception("Stock insuffisant."); // Vérifie le stock
        }
        $total += $item['price'] * $item['quantity']; // Total du panier
    }

    // Vérifie le solde utilisateur
    $stmt = $pdo->prepare("SELECT balance FROM User WHERE id = ? FOR UPDATE");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user['balance'] < $total) {
        throw new Exception("Solde insuffisant."); // Stop si solde insuffisant
    }

    // Déduction du solde
    $updateBalance = $pdo->prepare("UPDATE User SET balance = balance - ? WHERE id = ?");
    $updateBalance->execute([$total, $user_id]);

    // Mise à jour du stock
    foreach ($items as $item) {
        $updateStock = $pdo->prepare("UPDATE Stock SET quantity = quantity - ? WHERE article_id = ?");
        $updateStock->execute([$item['quantity'], $item['id']]);
    }

    // Création de la facture
    $insertInvoice = $pdo->prepare("
        INSERT INTO Invoice (user_id, amount, billing_address, billing_city, billing_zip_code)
        VALUES (?, ?, 'Adresse test', 'Ville test', '00000')
    ");
    $insertInvoice->execute([$user_id, $total]);

    // Vide le panier
    $clearCart = $pdo->prepare("DELETE FROM Cart WHERE user_id = ?");
    $clearCart->execute([$user_id]);

    $pdo->commit(); // Valide la transaction
    echo "Paiement effectué avec succès !";

} catch (Exception $e) {
    $pdo->rollBack(); // Annule si erreur
    echo "Erreur : " . $e->getMessage();
}