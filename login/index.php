<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

start_app_session();

if (is_authenticated()) {
    header('Location: ../home/');
    exit;
}

$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim((string)($_POST['identifier'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($identifier === '') {
        $errors[] = 'Email ou username obligatoire.';
    }

    if ($password === '') {
        $errors[] = 'Mot de passe obligatoire.';
    }

    if (!$errors) {
        try {
            /** @var PDO $pdo */
            $pdo = require __DIR__ . '/../db.php';

            $stmt = $pdo->prepare(
                'SELECT id, username, email, password FROM `User` WHERE email = :email_identifier OR username = :username_identifier LIMIT 1'
            );
            $stmt->execute([
                'email_identifier' => $identifier,
                'username_identifier' => $identifier,
            ]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, (string)$user['password'])) {
                $errors[] = 'Identifiants invalides.';
            } else {
                login_user($user);
                header('Location: ../home/');
                exit;
            }
        } catch (Throwable $exception) {
            $errors[] = 'Erreur serveur ou base de donnees. Verifie la connexion MySQL.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../styles.css">
        <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet">
        <title>Shop | Connexion</title>
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

                    <form method="post" class="login__registre" action="" novalidate>
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
                            <a class="login__signin" href="../register/">Creer un compte</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
