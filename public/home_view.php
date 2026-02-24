<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ma Boutique E-Commerce</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur notre boutique</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="login.php">Connexion</a>
        </nav>
    </header>

    <main>
        <h2>Articles récents</h2>
        <div class="articles-grid">
            <?php if (empty($articles)): ?>
                <p>Aucun article n'est disponible pour le moment.</p>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <article class="product-card">
                        <img src="<?= htmlspecialchars($article['lien_image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
                        <h3><?= htmlspecialchars($article['nom']) ?></h3>
                        <p><?= htmlspecialchars(substr($article['description'], 0, 100)) ?>...</p>
                        <p><strong>Prix : <?= number_format($article['prix'], 2) ?> €</strong></p>
                        <p><small>Publié par : <?= htmlspecialchars($article['username']) ?></small></p>
                        
                        <a href="detail.php?id=<?= $article['id'] ?>" class="btn">Voir le produit</a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>