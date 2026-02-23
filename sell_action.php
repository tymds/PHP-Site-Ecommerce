<?php
session_start();
require_once 'config/database.php';
if(!isset($_SESSION['/*SQL_userID*/'])) {
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: ../sell.php");
    exit();
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
$stock = filter_var($_POST['stock'] ?? 0, FILTER_VALIDATE_INT);
$author_id = $_SESSION['/*SQL_userID*/'];
if(empty($title) || empty($description) || $price === false || $stock === false) {
    die("All fields are required and must be valid.");
}

$oursql->begin_transaction();

try {

    $stmt = $oursql->prepare("INSERT INTO /*SQL_ARTICLE*/ (/*SQL_TITLE*/, /*SQL_CONTENT*/, /*SQL_PRICE*/, /*SQL_STOCK*/, /*SQL_AUTHOR_ID*/) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $title, $description, $price, $stock, $author_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert article: " . $stmt->error);
    }
    
    $stmt2 = $oursql->prepare("INSERT INTO /*SQL_STOCK*/ (/*SQL_ARTICLE_ID*/, /*SQL_QUANTITY*/) VALUES (?, ?)");
    $stmt2->bind_param("ii", $stmt->insert_id, $stock);
    if (!$stmt2->execute()) {
        throw new Exception("Failed to insert stock: " . $stmt2->error);
    }

    $oursql->commit();
    header("Location: index.php");
    exit();

} catch (Exception $e) {
    $oursql->rollback();
    die("Transaction failed: " . $e->getMessage());
}
?>