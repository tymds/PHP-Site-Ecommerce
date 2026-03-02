<?php
require_once 'config/database.php';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($article_id <= 0) {
    header("Location: index.php");
    exit();
}

try {

    $stmt = $mysqli->prepare("
        SELECT a.*, u.username, s.nombre_article as stock 
        FROM Article a 
        JOIN User u ON a.id_auteur = u.id 
        LEFT JOIN Stock s ON a.id = s.id_article 
        WHERE a.id = ?
    ");
    
    $stmt->bind_param("i", $article_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $article = $result->fetch_assoc();

    if (!$article) {
        die("Cet article n'existe pas.");
    }

    include 'views/detail_view.php';

} catch (Exception $e) {
    error_log($e->getMessage());
    die("Erreur lors de la récupération du produit.");
}