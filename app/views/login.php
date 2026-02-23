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
                        <h2 class="login__heading">Connexion</h2>
                        <p class="login__subtitle">Connecte-toi pour acceder a ton espace client.</p>
                    </div>

                    <form method="post" class="login__registre" action="<?= htmlspecialchars(app_url('/login'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
                        <h1 class="login__title">Se connecter</h1>

                        <?php if ($errors): ?>
                            <div class="form__message form__message--error">
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="login__box">
                            <i class="bx bx-user login__icon"></i>
                            <input
                                type="text"
                                name="identifier"
                                class="login__input"
                                placeholder="Email ou username"
                                value="<?= htmlspecialchars($identifier, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="login__box">
                            <i class="bx bx-lock-alt login__icon"></i>
                            <input
                                type="password"
                                name="password"
                                class="login__input"
                                placeholder="Mot de passe"
                                required
                            >
                        </div>

                        <button type="submit" class="login__button">Connexion</button>

                        <div>
                            <span class="login__account">Nouveau client ?</span>
                            <a class="login__signin" href="<?= htmlspecialchars(app_url('/register'), ENT_QUOTES, 'UTF-8') ?>">Creer un compte</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
