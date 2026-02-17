<?php
declare(strict_types=1);

require_once __DIR__ . '/../auth.php';

start_app_session();

if (!is_authenticated()) {
    header('Location: ../login/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../styles.css">
        <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet">
        <title>Shop | Home</title>
    </head>
    <body>
        <div class="login">
            <div class="login__content">
                <div class="login__forms">
                    <div class="login__header">
                        <span class="login__badge"><i class="bx bx-store-alt"></i> Shop</span>
                        <h2 class="login__heading">Home</h2>
                        <p class="login__subtitle">Bienvenue <?= htmlspecialchars((string)$_SESSION['username'], ENT_QUOTES, 'UTF-8') ?>.</p>
                    </div>

                    <div class="login__registre">
                        <p class="form__message form__message--success">Tu es bien connecte.</p>
                        <p class="login__subtitle">Email: <?= htmlspecialchars((string)$_SESSION['email'], ENT_QUOTES, 'UTF-8') ?></p>
                        <a class="login__button" href="../logout/">Se deconnecter</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

