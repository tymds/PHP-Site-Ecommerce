<form action="sell_action.php" method="POST">
    <input type="text" name="nom" placeholder="Nom de l'article" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="number" step="0.01" name="prix" placeholder="Prix" required>
    <input type="number" name="stock" placeholder="Quantité en stock" required>
    <input type="text" name="image_url" placeholder="Lien de l'image (URL)" required>
    <button type="submit">Mettre en vente</button>
</form>