<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - E-Commerce</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="sell.php">Vendre un article</a>
        </nav>
    </header>

    <main>
        <h1>Derniers articles en vente</h1>
        <div class="product-container">
            <?php foreach ($articles as $article): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($article['lien_image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
                    
                    <h3><?= htmlspecialchars($article['nom']) ?></h3>
                    
                    <p><?= htmlspecialchars(substr($article['description'], 0, 150)) ?>...</p>
                    
                    <p class="price"><?= number_format($article['prix'], 2, ',', ' ') ?> €</p>
                    
                    <p class="author">Vendu par : <?= htmlspecialchars($article['username']) ?></p>
                    
                    <a href="detail.php?id=<?= (int)$article['id'] ?>" class="btn">Voir le produit</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>