<?php
declare(strict_types=1);
require_once __DIR__ . '/../auth.php';
start_app_session();
$user_id = $_SESSION['user_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<h1>Mon Panier</h1>

<?php if (empty($items)): ?>
    <p>Votre panier est vide.</p>
<?php else: ?>
    <table border="1">
        <thead>
            <tr>
                <th>Article</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Sous-total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= number_format($item['price'], 2, ',', ' ') ?> €</td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td>
                        <?php 
                        $subtotal = $item['price'] * $item['quantity'];
                        echo number_format($subtotal, 2, ',', ' ') . " €"; 
                        $total += $subtotal;
                        ?>
                    </td>
                    <td>
                        <a href="CartController.php?action=remove&id=<?= $item['id'] ?>">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total : <?= number_format($total, 2, ',', ' ') ?> €</h3>

    <form action="../controllers/PaymentController.php" method="POST">
        <button type="submit">Valider le panier</button>
    </form>
<?php endif; ?>

<p><a href="../index.php">Retour à la boutique</a></p>

</body>
</html>