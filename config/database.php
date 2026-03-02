<?php
// Connexion à la base de données avec PDO
$host = 'localhost'; // Adresse du serveur
$db   = 'nom_de_ta_bdd'; // Nom de ta base
$user = 'root'; // Utilisateur MySQL
$pass = '';     // Mot de passe MySQL
$charset = 'utf8mb4'; // Encodage

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; // DSN PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Affiche les erreurs SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retour sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vrais prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); // Crée la connexion
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode()); // Gère l’erreur si connexion impossible
}