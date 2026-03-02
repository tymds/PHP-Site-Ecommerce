<?php
declare(strict_types=1);

if (!is_post_request()) {
    redirect_to('/sell');
}

if (!is_authenticated()) {
    redirect_to('/login');
}

$errors = [];

if (!is_valid_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
    $errors[] = 'Session invalide, recharge la page puis reessaie.';
} else {
    $name = trim((string)($_POST['name'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $priceInput = (string)($_POST['price'] ?? '');
    $quantityInput = (string)($_POST['quantity'] ?? '');
    $imageUrl = trim((string)($_POST['image_url'] ?? ''));
    $authorId = (int)($_SESSION['user_id'] ?? 0);

    if ($name === '') {
        $errors[] = 'Le nom de l\'article est obligatoire.';
    } elseif (strlen($name) > 150) {
        $errors[] = 'Le nom ne doit pas dépasser 150 caractères.';
    }

    if ($description === '') {
        $errors[] = 'La description est obligatoire.';
    }

    if ($priceInput === '') {
        $errors[] = 'Le prix est obligatoire.';
    } else {
        $price = filter_var($priceInput, FILTER_VALIDATE_FLOAT);
        if ($price === false || $price < 0) {
            $errors[] = 'Le prix doit être un nombre valide et positif.';
        }
    }

    if ($quantityInput === '') {
        $errors[] = 'La quantité est obligatoire.';
    } else {
        $quantity = filter_var($quantityInput, FILTER_VALIDATE_INT);
        if ($quantity === false || $quantity <= 0) {
            $errors[] = 'La quantité doit être un nombre entier positif.';
        }
    }

    if ($imageUrl === '') {
        $errors[] = 'L\'URL de l\'image est obligatoire.';
    } elseif (filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
        $errors[] = 'L\'URL de l\'image n\'est pas valide.';
    }

    if (!$errors) {
        try {
            /** @var PDO $pdo */
            $pdo = require __DIR__ . '/../../private/db.php';

            $pdo->beginTransaction();

            // Insert article using correct schema
            $insertArticleStmt = $pdo->prepare(
                'INSERT INTO `Article` (name, description, price, published_at, author_id, image_url)
                VALUES (:name, :description, :price, CURRENT_TIMESTAMP, :author_id, :image_url)'
            );
            $insertArticleStmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'author_id' => $authorId,
                'image_url' => $imageUrl,
            ]);

            $articleId = (int)$pdo->lastInsertId();

            // Insert stock record
            $insertStockStmt = $pdo->prepare(
                'INSERT INTO `Stock` (article_id, quantity) VALUES (:article_id, :quantity)'
            );
            $insertStockStmt->execute([
                'article_id' => $articleId,
                'quantity' => $quantity,
            ]);

            $pdo->commit();
            redirect_to('/home');
        } catch (Throwable $exception) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $errors[] = 'Erreur serveur. Impossible de mettre l\'article en vente.';
            error_log($exception->getMessage());
        }
    }
}

require __DIR__ . '/../views/sell_view.php';