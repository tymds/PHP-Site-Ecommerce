<?php
declare(strict_types=1);

if (is_authenticated()) {
    redirect_to('/home');
}

$errors = [];
$identifier = '';
$pageTitle = 'Shop | Connexion';

if (is_post_request()) {
    if (!is_valid_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Session invalide, recharge la page puis reessaie.';
    } elseif (is_login_rate_limited()) {
        $retryAfter = login_retry_after_seconds();
        $errors[] = 'Trop de tentatives. Reessaie dans ' . $retryAfter . ' secondes.';
    } else {
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
                $pdo = require __DIR__ . '/../../private/db.php';

                $stmt = $pdo->prepare(
                    'SELECT id, username, email, password FROM `User` WHERE email = :email_identifier OR username = :username_identifier LIMIT 1'
                );
                $stmt->execute([
                    'email_identifier' => $identifier,
                    'username_identifier' => $identifier,
                ]);
                $user = $stmt->fetch();

                if (!$user || !password_verify($password, (string)$user['password'])) {
                    register_login_attempt(false);
                    $errors[] = 'Identifiants invalides.';
                } else {
                    register_login_attempt(true);
                    login_user($user);
                    redirect_to('/home');
                }
            } catch (Throwable $exception) {
                $errors[] = 'Erreur serveur ou base de donnees. Verifie la connexion MySQL.';
            }
        }
    }
}

require __DIR__ . '/../views/login.php';
