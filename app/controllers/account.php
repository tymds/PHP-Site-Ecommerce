<?php
declare(strict_types=1);

const ACCOUNT_TOPUP_MAX_PER_OPERATION_CENTS = 50000; // 500.00 EUR
const ACCOUNT_MAX_BALANCE_CENTS = 200000; // 2000.00 EUR
const ACCOUNT_SENSITIVE_MAX_ATTEMPTS = 5;
const ACCOUNT_SENSITIVE_WINDOW_SECONDS = 600;
const ACCOUNT_DELETE_CONFIRMATION_TEXT = 'SUPPRIMER MON COMPTE';

function account_decimal_to_cents(string $rawAmount): ?int
{
    $normalized = str_replace(',', '.', trim($rawAmount));

    if (!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/', $normalized)) {
        return null;
    }

    $parts = explode('.', $normalized, 2);
    $whole = (int)$parts[0];
    $fraction = isset($parts[1]) ? str_pad(substr($parts[1], 0, 2), 2, '0') : '00';

    return ($whole * 100) + (int)$fraction;
}

function account_cents_to_decimal(int $amountCents): string
{
    return number_format($amountCents / 100, 2, '.', '');
}

function account_verify_current_password(PDO $pdo, int $userId, string $password): bool
{
    if ($password === '') {
        return false;
    }

    $passwordStmt = $pdo->prepare('SELECT password FROM `User` WHERE id = :id LIMIT 1');
    $passwordStmt->execute(['id' => $userId]);
    $passwordRow = $passwordStmt->fetch();

    if (!$passwordRow) {
        return false;
    }

    return password_verify($password, (string)$passwordRow['password']);
}

function account_get_recent_sensitive_attempts(): array
{
    $attempts = $_SESSION['account_sensitive_attempts'] ?? [];

    if (!is_array($attempts)) {
        return [];
    }

    $now = time();
    $filtered = [];

    foreach ($attempts as $attempt) {
        $timestamp = (int)$attempt;
        if ($timestamp > 0 && ($now - $timestamp) < ACCOUNT_SENSITIVE_WINDOW_SECONDS) {
            $filtered[] = $timestamp;
        }
    }

    $_SESSION['account_sensitive_attempts'] = $filtered;

    return $filtered;
}

function account_is_sensitive_rate_limited(): bool
{
    return count(account_get_recent_sensitive_attempts()) >= ACCOUNT_SENSITIVE_MAX_ATTEMPTS;
}

function account_sensitive_retry_after_seconds(): int
{
    $attempts = account_get_recent_sensitive_attempts();

    if (count($attempts) < ACCOUNT_SENSITIVE_MAX_ATTEMPTS) {
        return 0;
    }

    $oldestAttempt = min($attempts);
    $retryAfter = ACCOUNT_SENSITIVE_WINDOW_SECONDS - (time() - $oldestAttempt);

    return max(0, $retryAfter);
}

function account_register_sensitive_attempt(bool $success): void
{
    if ($success) {
        unset($_SESSION['account_sensitive_attempts']);
        return;
    }

    $attempts = account_get_recent_sensitive_attempts();
    $attempts[] = time();
    $_SESSION['account_sensitive_attempts'] = $attempts;
}

function account_rotate_csrf_token(): void
{
    unset($_SESSION['csrf_token']);
    csrf_token();
}

function account_mask_email(string $email): string
{
    if (!str_contains($email, '@')) {
        return 'Email prive';
    }

    [$localPart, $domainPart] = explode('@', $email, 2);
    $localFirst = substr($localPart, 0, 1);
    $domainFirst = substr($domainPart, 0, 1);

    if ($localFirst === '' || $domainFirst === '') {
        return 'Email prive';
    }

    return $localFirst . '***@' . $domainFirst . '***';
}

if (!is_authenticated()) {
    redirect_to('/login');
}

$errors = [];
$successes = [];
$pageTitle = 'Shop | Compte';
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$requestedId = (string)($_GET['id'] ?? '');
$targetUserId = $currentUserId;
$accountEmailForDisplay = '';
$topupMaxPerOperationDisplay = number_format(ACCOUNT_TOPUP_MAX_PER_OPERATION_CENTS / 100, 2, ',', ' ');
$maxBalanceDisplay = number_format(ACCOUNT_MAX_BALANCE_CENTS / 100, 2, ',', ' ');
$topupMaxPerOperationInput = account_cents_to_decimal(ACCOUNT_TOPUP_MAX_PER_OPERATION_CENTS);

if ($requestedId !== '') {
    if (!ctype_digit($requestedId) || (int)$requestedId <= 0) {
        $errors[] = 'Le parametre id est invalide.';
    } else {
        $targetUserId = (int)$requestedId;
    }
}

$isOwnAccount = $targetUserId === $currentUserId;

try {
    /** @var PDO $pdo */
    $pdo = require __DIR__ . '/../../private/db.php';
} catch (Throwable $exception) {
    $errors[] = 'Erreur serveur ou base de donnees. Verifie la connexion MySQL.';
}

if (isset($pdo) && is_post_request()) {
    if (!$isOwnAccount) {
        http_response_code(403);
        $errors[] = 'Tu ne peux pas modifier les informations dun autre compte.';
    } elseif (!is_valid_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
        http_response_code(400);
        $errors[] = 'Session invalide, recharge la page puis reessaie.';
    } else {
        $action = (string)($_POST['action'] ?? '');
        $isSensitiveAction = in_array($action, ['update_email', 'update_password', 'add_balance', 'delete_account'], true);
        $actionSucceeded = false;

        if ($isSensitiveAction && account_is_sensitive_rate_limited()) {
            http_response_code(429);
            $errors[] = 'Trop de tentatives sensibles. Reessaie dans ' . account_sensitive_retry_after_seconds() . ' secondes.';
        } elseif ($action === 'update_email') {
            $newEmail = strtolower(trim((string)($_POST['email'] ?? '')));
            $currentPassword = (string)($_POST['current_password'] ?? '');

            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide.';
            } elseif (!account_verify_current_password($pdo, $currentUserId, $currentPassword)) {
                $errors[] = 'Mot de passe actuel invalide.';
            } else {
                $checkEmailStmt = $pdo->prepare(
                    'SELECT id FROM `User` WHERE email = :email AND id != :id LIMIT 1'
                );
                $checkEmailStmt->execute([
                    'email' => $newEmail,
                    'id' => $currentUserId,
                ]);

                if ($checkEmailStmt->fetch()) {
                    $errors[] = 'Cette adresse email est deja utilisee.';
                } else {
                    $updateEmailStmt = $pdo->prepare('UPDATE `User` SET email = :email WHERE id = :id');
                    $updateEmailStmt->execute([
                        'email' => $newEmail,
                        'id' => $currentUserId,
                    ]);
                    $_SESSION['email'] = $newEmail;
                    $successes[] = 'Adresse email mise a jour.';
                    $actionSucceeded = true;
                }
            }
        } elseif ($action === 'update_password') {
            $currentPassword = (string)($_POST['current_password'] ?? '');
            $newPassword = (string)($_POST['new_password'] ?? '');
            $confirmPassword = (string)($_POST['confirm_password'] ?? '');

            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                $errors[] = 'Tous les champs mot de passe sont obligatoires.';
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,72}$/', $newPassword)) {
                $errors[] = 'Le nouveau mot de passe doit faire 8 a 72 caracteres et contenir au moins 1 lettre et 1 chiffre.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'La confirmation du nouveau mot de passe est incorrecte.';
            } elseif ($currentPassword === $newPassword) {
                $errors[] = 'Le nouveau mot de passe doit etre different de l ancien.';
            } elseif (!account_verify_current_password($pdo, $currentUserId, $currentPassword)) {
                $errors[] = 'Le mot de passe actuel est incorrect.';
            } else {
                $updatePasswordStmt = $pdo->prepare('UPDATE `User` SET password = :password WHERE id = :id');
                $updatePasswordStmt->execute([
                    'password' => password_hash($newPassword, PASSWORD_BCRYPT),
                    'id' => $currentUserId,
                ]);
                $successes[] = 'Mot de passe mis a jour.';
                $actionSucceeded = true;
            }
        } elseif ($action === 'add_balance') {
            $amountInput = (string)($_POST['amount'] ?? '');
            $currentPassword = (string)($_POST['current_password'] ?? '');
            $amountCents = account_decimal_to_cents($amountInput);

            if ($amountCents === null) {
                $errors[] = 'Montant invalide.';
            } elseif (!account_verify_current_password($pdo, $currentUserId, $currentPassword)) {
                $errors[] = 'Mot de passe actuel invalide.';
            } elseif ($amountCents <= 0) {
                $errors[] = 'Le montant doit etre superieur a 0.';
            } elseif ($amountCents > ACCOUNT_TOPUP_MAX_PER_OPERATION_CENTS) {
                $errors[] = 'Le montant maximum par ajout est ' . number_format(ACCOUNT_TOPUP_MAX_PER_OPERATION_CENTS / 100, 2, ',', ' ') . ' EUR.';
            } else {
                try {
                    $pdo->beginTransaction();

                    $balanceStmt = $pdo->prepare('SELECT balance FROM `User` WHERE id = :id LIMIT 1 FOR UPDATE');
                    $balanceStmt->execute(['id' => $currentUserId]);
                    $balanceRow = $balanceStmt->fetch();

                    if (!$balanceRow) {
                        throw new RuntimeException('Compte introuvable.');
                    }

                    $currentBalanceCents = account_decimal_to_cents((string)$balanceRow['balance']);
                    if ($currentBalanceCents === null) {
                        throw new RuntimeException('Solde invalide en base.');
                    }

                    $newBalanceCents = $currentBalanceCents + $amountCents;

                    if ($newBalanceCents > ACCOUNT_MAX_BALANCE_CENTS) {
                        $pdo->rollBack();
                        $errors[] = 'Le solde maximal autorise est ' . number_format(ACCOUNT_MAX_BALANCE_CENTS / 100, 2, ',', ' ') . ' EUR.';
                    } else {
                        $updateBalanceStmt = $pdo->prepare('UPDATE `User` SET balance = :balance WHERE id = :id');
                        $updateBalanceStmt->execute([
                            'balance' => account_cents_to_decimal($newBalanceCents),
                            'id' => $currentUserId,
                        ]);
                        $pdo->commit();
                        $successes[] = 'Le solde a ete credite de ' . number_format($amountCents / 100, 2, ',', ' ') . ' EUR.';
                        $actionSucceeded = true;
                    }
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $errors[] = 'Impossible de crediter le solde pour le moment.';
                }
            }
        } elseif ($action === 'delete_account') {
            $currentPassword = (string)($_POST['current_password'] ?? '');
            $confirmation = trim((string)($_POST['confirmation_text'] ?? ''));

            if ($confirmation !== ACCOUNT_DELETE_CONFIRMATION_TEXT) {
                $errors[] = 'Texte de confirmation invalide.';
            } elseif (!account_verify_current_password($pdo, $currentUserId, $currentPassword)) {
                $errors[] = 'Mot de passe actuel invalide.';
            } else {
                try {
                    $pdo->beginTransaction();

                    $deleteStmt = $pdo->prepare('DELETE FROM `User` WHERE id = :id');
                    $deleteStmt->execute(['id' => $currentUserId]);

                    if ($deleteStmt->rowCount() !== 1) {
                        throw new RuntimeException('Suppression impossible.');
                    }

                    $pdo->commit();
                    account_register_sensitive_attempt(true);
                    account_rotate_csrf_token();
                    session_regenerate_id(true);
                    logout_user();
                    redirect_to('/home');
                } catch (Throwable $exception) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $errors[] = 'Suppression de compte impossible pour le moment.';
                }
            }
        } else {
            $errors[] = 'Action invalide.';
        }

        if ($isSensitiveAction) {
            account_register_sensitive_attempt($actionSucceeded);

            if ($actionSucceeded) {
                account_rotate_csrf_token();
                session_regenerate_id(true);
            }
        }
    }
}

$account = null;
$publishedArticles = [];
$invoices = [];
$purchasedArticles = [];

if (isset($pdo)) {
    try {
        $accountStmt = $pdo->prepare(
            'SELECT username, email, balance, role FROM `User` WHERE id = :id LIMIT 1'
        );
        $accountStmt->execute(['id' => $targetUserId]);
        $account = $accountStmt->fetch();

        if (!$account) {
            http_response_code(404);
            $errors[] = 'Ce compte est introuvable.';
        } else {
            $accountEmailForDisplay = $isOwnAccount
                ? (string)$account['email']
                : account_mask_email((string)$account['email']);

            $articlesStmt = $pdo->prepare(
                'SELECT id, name, description, price, published_at
                 FROM `Article`
                 WHERE author_id = :author_id
                 ORDER BY published_at DESC'
            );
            $articlesStmt->execute(['author_id' => $targetUserId]);
            $publishedArticles = $articlesStmt->fetchAll();

            if ($isOwnAccount) {
                $invoiceStmt = $pdo->prepare(
                    'SELECT id, transaction_date, amount, billing_address, billing_city, billing_zip_code
                     FROM `Invoice`
                     WHERE user_id = :user_id
                     ORDER BY transaction_date DESC'
                );
                $invoiceStmt->execute(['user_id' => $currentUserId]);
                $invoices = $invoiceStmt->fetchAll();

                // The provided schema has no invoice line table, so Cart is used as available purchase history source.
                $purchasedStmt = $pdo->prepare(
                    'SELECT a.id, a.name, a.price, a.published_at, u.username AS author_username
                     FROM `Cart` c
                     INNER JOIN `Article` a ON a.id = c.article_id
                     LEFT JOIN `User` u ON u.id = a.author_id
                     WHERE c.user_id = :user_id
                     ORDER BY c.id DESC'
                );
                $purchasedStmt->execute(['user_id' => $currentUserId]);
                $purchasedArticles = $purchasedStmt->fetchAll();
            }
        }
    } catch (Throwable $exception) {
        $errors[] = 'Erreur lors du chargement du compte.';
    }
}

require __DIR__ . '/../views/account.php';
