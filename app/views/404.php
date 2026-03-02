<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?= htmlspecialchars(app_url('/assets/styles.css'), ENT_QUOTES, 'UTF-8') ?>">
        <title>404 | Shop</title>
    </head>
    <body>
        <div class="login">
            <div class="login__content">
                <div class="login__forms">
                    <div class="login__header">
                        <span class="login__badge">Shop</span>
                        <h2 class="login__heading">Page introuvable</h2>
                        <p class="login__subtitle">La route demandee n'existe pas.</p>
                    </div>

                    <div class="login__registre">
                        <a class="login__button" href="<?= htmlspecialchars(app_url('/home'), ENT_QUOTES, 'UTF-8') ?>">Retour a l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
