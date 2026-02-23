<?php
declare(strict_types=1);

if (is_authenticated()) {
    redirect_to('/home');
}

$errors = [];
$username = '';
$email = '';
$pageTitle = 'Shop | Inscription';

if (is_post_request()) {
    if (!is_valid_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Session invalide, recharge la page puis reessaie.';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $usernameLength = function_exists('mb_strlen') ? mb_strlen($username) : strlen($username);

        if ($usernameLength < 3 || $usernameLength > 50) {
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
                $pdo = require __DIR__ . '/../../private/db.php';

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

                    redirect_to('/home');
                }
            } catch (Throwable $exception) {
                $errors[] = 'Erreur serveur ou base de donnees. Verifie la connexion MySQL.';
            }
        }
    }
}

require __DIR__ . '/../views/register.php';
