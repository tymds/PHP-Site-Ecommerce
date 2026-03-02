<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['nom']) ?> - Boutique</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <a href="index.php">← Retour à l'accueil</a>
    </header>
    <main class="product-detail">
        <div class="product-image">
            <img src="<?= htmlspecialchars($article['lien_image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
        </div>
        <div class="product-info">
            <h1><?= htmlspecialchars($article['nom']) ?></h1>
            
            <p class="author">Mis en vente par : <strong><?= htmlspecialchars($article['username']) ?></strong></p>
            
            <div class="description">
                <h3>Description</h3>
                <p><?= nl2br(htmlspecialchars($article['description'])) ?></p>
            </div>
            <p class="price">Prix : <?= number_format($article['prix'], 2, ',', ' ') ?> €</p>
            <div class="stock-status">
                <?php if ($article['stock'] > 0): ?>
                    <span class="in-stock">En stock (<?= (int)$article['stock'] ?> disponibles)</span>
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="article_id" value="<?= (int)$article['id'] ?>">
                        <button type="submit" class="btn-buy">Ajouter au panier</button>
                    </form>
                <?php else: ?>
                    <span class="out-of-stock">Rupture de stock</span>
                <?php endif; ?>
            </div>
            <p class="date"><small>Publié le : <?= date('d/m/Y', strtotime($article['date_publication'])) ?></small></p>
        </div>
    </main>
</body>
</html>