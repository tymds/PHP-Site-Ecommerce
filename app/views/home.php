<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - E-Commerce</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('/assets/styles.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header>
        <nav>
            <a href="<?= htmlspecialchars(app_url('/home'), ENT_QUOTES, 'UTF-8') ?>">Accueil</a>
            <a href="<?= htmlspecialchars(app_url('/sell'), ENT_QUOTES, 'UTF-8') ?>">Vendre un article</a>
            <?php if ($authenticated): ?>
                <a href="<?= htmlspecialchars(app_url('/account'), ENT_QUOTES, 'UTF-8') ?>">Profil</a>
                <form method="post" action="<?= htmlspecialchars(app_url('/logout'), ENT_QUOTES, 'UTF-8') ?>" class="nav-logout-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="nav-logout-btn">Logout</button>
                </form>
            <?php else: ?>
                <a href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">Connexion</a>
                <a href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>">Inscription</a>
            <?php endif; ?>
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
