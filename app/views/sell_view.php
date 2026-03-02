<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Mettre en vente - E-Commerce', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('/assets/styles.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <div class="form-container">
        <h1>Vendre un article</h1>

        <?php if (!empty($errors) && is_array($errors)): ?>
            <div class="form__message form__message--error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= htmlspecialchars(app_url('/sell-action'), ENT_QUOTES, 'UTF-8') ?>" method="post" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="field">
                <label for="name">Nom de l'article</label>
                <input type="text" id="name" name="name" placeholder="Ex: Casque Bluetooth" required value="<?= htmlspecialchars((string)($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="field">
                <label for="description">Description de l'article</label>
                <textarea id="description" name="description" placeholder="Detaillez votre produit..." required><?= htmlspecialchars((string)($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="row">
                <div class="field">
                    <label for="price">Prix (en EUR)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" required value="<?= htmlspecialchars((string)($_POST['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="field">
                    <label for="quantity">Quantite disponible</label>
                    <input type="number" id="quantity" name="quantity" min="1" placeholder="1" required value="<?= htmlspecialchars((string)($_POST['quantity'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="field">
                <label for="image_url">Lien de l'image</label>
                <input type="url" id="image_url" name="image_url" placeholder="https://exemple.com/image.jpg" required value="<?= htmlspecialchars((string)($_POST['image_url'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn-submit">Mettre en vente</button>
        </form>
    </div>
</body>
</html>
