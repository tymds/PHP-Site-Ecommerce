<?php
declare(strict_types=1);

require_once __DIR__ . '/../private/auth.php';

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
            $pdo = require __DIR__ . '/../private/db.php';

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

require __DIR__ . '/view.php';
