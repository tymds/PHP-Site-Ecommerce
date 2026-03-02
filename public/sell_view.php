<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mettre en vente - E-Commerce</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Vendre un article</h1>
        <form action="sell_action.php" method="POST">
            <div class="field">
                <label for="nom">Nom de l'article</label>
                <input type="text" id="nom" name="nom" placeholder="Ex: Casque Bluetooth" required>
            </div>
            <div class="field">
                <label for="description">Description de l'article</label>
                <textarea id="description" name="description" placeholder="Détaillez votre produit..." required></textarea>
            </div>
            <div class="row">
                <div class="field">
                    <label for="prix">Prix (en €)</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" placeholder="0.00" required>
                </div>
                <div class="field">
                    <label for="stock">Quantité disponible</label>
                    <input type="number" id="stock" name="stock" min="1" placeholder="1" required>
                </div>
            </div>
            <div class="field">
                <label for="image_url">Lien de l'image</label>
                <input type="url" id="image_url" name="image_url" placeholder="https://exemple.com/image.jpg" required>
            </div>
            <button type="submit" class="btn-submit">Mettre en vente</button>
        </form>
    </div>
</body>
</html>