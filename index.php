<?php
// index.php
require_once 'config/database.php';

try {
    // recent as first
    $query = "SELECT a.*, u.username FROM Article a  JOIN User u ON a.id_auteur = u.id  ORDER BY a.date_publication DESC";
    
    $result = $mysqli->query($query);
    $articles = $result->fetch_all(MYSQLI_ASSOC);
    include 'views/home_view.php';

} catch (Exception $e) {

    error_log($e->getMessage());
    die("Une erreur est survenue lors du chargement des articles.");
}