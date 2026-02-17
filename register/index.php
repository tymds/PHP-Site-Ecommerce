<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

start_app_session();

if (is_authenticated()) {
    header('Location: ../home/');
    exit;
}

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($username === '' || strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Le nom utilisateur doit contenir entre 3 et 50 caracteres.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caracteres.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'La confirmation du mot de passe ne correspond pas.';
    }

    if (!$errors) {
        try {
            /** @var PDO $pdo */
            $pdo = require __DIR__ . '/../db.php';

            $checkStmt = $pdo->prepare(
                'SELECT id FROM `User` WHERE username = :username OR email = :email LIMIT 1'
            );
            $checkStmt->execute([
                'username' => $username,
                'email' => $email,
            ]);

            if ($checkStmt->fetch()) {
                $errors[] = 'Ce nom utilisateur ou cet email est deja utilise.';
            } else {
                $insertStmt = $pdo->prepare(
                    'INSERT INTO `User` (username, password, email) VALUES (:username, :password, :email)'
                );

                $insertStmt->execute([
                    'username' => $username,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'email' => $email,
                ]);

                login_user([
                    'id' => (int)$pdo->lastInsertId(),
                    'username' => $username,
                    'email' => $email,
                ]);

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
        <title>Shop | Inscription</title>
    </head>
    <body>
        <div class="login">
            <div class="login__content">
                <div class="login__forms">
                    <div class="login__header">
                        <span class="login__badge"><i class="bx bx-store-alt"></i> Shop</span>
                        <h2 class="login__heading">Inscription</h2>
                        <p class="login__subtitle">Cree ton compte client pour commander plus vite.</p>
                    </div>

                    <form method="post" class="login__registre" action="" novalidate>
                        <h1 class="login__title">Creer un compte</h1>

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
                                name="username"
                                class="login__input"
                                placeholder="Nom utilisateur"
                                value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="login__box">
                            <i class="bx bx-at login__icon"></i>
                            <input
                                type="email"
                                name="email"
                                class="login__input"
                                placeholder="Adresse email"
                                value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
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

                        <div class="login__box">
                            <i class="bx bx-lock login__icon"></i>
                            <input
                                type="password"
                                name="confirm_password"
                                class="login__input"
                                placeholder="Confirmer le mot de passe"
                                required
                            >
                        </div>

                        <button type="submit" class="login__button">Creer mon compte</button>

                        <div>
                            <span class="login__account">Deja inscrit ?</span>
                            <a class="login__signup" href="../login/">Se connecter</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

