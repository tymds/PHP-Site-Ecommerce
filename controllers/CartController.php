<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../auth.php';

start_app_session(); // Démarre la session
$user_id = $_SESSION['user_id'] ?? null; // Récupère l’utilisateur connecté
$cart = new Cart(); // Instancie le modèle Cart

// Récupère l’action demandée : add, remove ou index
$action = $_GET['action'] ?? 'index';

// Ajouter un article
if ($action === 'add' && isset($_GET['id'])) {
    $cart->add($user_id, (int)$_GET['id']); // Ajoute l’article
    header('Location: /controllers/CartController.php'); // Redirection vers le panier
    exit;
}

// Supprimer un article
if ($action === 'remove' && isset($_GET['id'])) {
    $cart->remove($user_id, (int)$_GET['id']); // Supprime l’article
    header('Location: /controllers/CartController.php'); // Redirection
    exit;
}

// Récupère tous les articles du panier pour l’utilisateur
$items = $cart->getUserCart($user_id);

// Charge la vue
require __DIR__ . '/../views/cart.php';