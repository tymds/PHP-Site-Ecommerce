# PHP-Site-Ecommerce

Guide rapide pour lancer le projet avec XAMPP (Windows).

## Prérequis

- XAMPP installé (`Apache` + `MySQL`)
- Projet placé dans `C:\xampp\htdocs\PHP-Site-Ecommerce`

## Lancer le projet

1. Ouvrez le panneau XAMPP.
2. Démarrez `Apache` et `MySQL`.
3. Ouvrez `http://localhost/phpmyadmin`.
4. Créez une base de données nommée `php_exam`.
5. Ouvrez le fichier `private/database.sql`, copiez tout son contenu.
6. Dans phpMyAdmin, sélectionnez la base `php_exam`, ouvrez l'onglet `SQL`, collez le contenu, puis exécutez.
7. Ouvrez dans votre navigateur :
   - `http://localhost/PHP-Site-Ecommerce/`

## Configuration base de données

Le projet lit la connexion dans `private/db.php` :

- `DB_HOST` (défaut : `127.0.0.1`)
- `DB_PORT` (défaut : `3306`)
- `DB_NAME` (défaut : `php_exam`)
- `DB_USER` (défaut : `root`)
- `DB_PASS` (défaut : vide)

Si vous utilisez la configuration XAMPP standard, les valeurs par défaut fonctionnent.

## Routes utiles

- `/` ou `/home` : accueil
- `/login` : connexion
- `/register` : inscription
- `/account` : compte utilisateur

## Dépannage rapide

- Si `http://localhost/PHP-Site-Ecommerce/` ne marche pas :
  - vérifiez que `Apache` est `Running`
  - vérifiez que le dossier est bien dans `htdocs`
  - vérifiez que le nom du dossier est exactement `PHP-Site-Ecommerce`
- Si vous avez une erreur de base de données :
  - vérifiez que `MySQL` est `Running`
  - vérifiez que la base `php_exam` existe
  - vérifiez les identifiants dans `private/db.php`
