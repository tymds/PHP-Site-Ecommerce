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
