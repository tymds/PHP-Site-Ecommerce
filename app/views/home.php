<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="<?= htmlspecialchars(app_url('/assets/styles.css'), ENT_QUOTES, 'UTF-8') ?>">
        <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet">
        <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    </head>
    <body>
        <div class="login">
            <div class="login__content">
                <div class="login__forms">
                    <div class="login__header">
                        <span class="login__badge"><i class="bx bx-store-alt"></i> Shop</span>
                        <h2 class="login__heading">Home</h2>
                        <?php if ($authenticated): ?>
                            <p class="login__subtitle">Bienvenue <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>.</p>
                        <?php else: ?>
                            <p class="login__subtitle">Tu n'es pas encore connecté.</p>
                        <?php endif; ?>
                    </div>

                    <div class="login__registre">
                        <?php if ($authenticated): ?>
                            <p class="form__message form__message--success">Tu es bien connecté.</p>
                            <p class="login__subtitle">Email: <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></p>

                            <form method="post" action="<?= htmlspecialchars(app_url('/logout'), ENT_QUOTES, 'UTF-8') ?>" class="login__inline-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="login__button">Se deconnecter</button>
                            </form>
                        <?php else: ?>
                            <a class="login__button" href="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>">Se connecter</a>
                            <a class="login__button" href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>">S'inscrire</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
