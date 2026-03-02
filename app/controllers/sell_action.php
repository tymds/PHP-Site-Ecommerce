<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // clean up and filter input
    $nom = htmlspecialchars(trim($_POST['nom']));
    $description = htmlspecialchars(trim($_POST['description']));
    $prix = filter_var($_POST['prix'], FILTER_VALIDATE_FLOAT);
    $quantite = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
    $image = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);
    $author_id = $_SESSION['user_id'];
    $date_now = date("Y-m-d H:i:s"); // Date de publication 
    if (!$nom || !$description || $prix === false || $quantite === false || !$image) {
        die("Données invalides ou manquantes.");
    }

    $mysqli->begin_transaction();

    try {
        // Insert
        $sqlArt = "INSERT INTO Article (nom, description, prix, date_publication, id_auteur, lien_image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sqlArt);
        $stmt->bind_param("ssdsss", $nom, $description, $prix, $date_now, $author_id, $image);
        $stmt->execute();
        
        $article_id = $mysqli->insert_id;

        // obligatory insert
        $sqlStock = "INSERT INTO Stock (id_article, nombre_article) VALUES (?, ?)";
        $stmt2 = $mysqli->prepare($sqlStock);
        $stmt2->bind_param("ii", $article_id, $quantite);
        $stmt2->execute();

        $mysqli->commit();
        header("Location: index.php"); // go home
        exit();

    } catch (Exception $e) {
        $mysqli->rollback();
        die("Erreur lors de la mise en vente : " . $e->getMessage());
    }
}