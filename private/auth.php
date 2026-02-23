<?php
declare(strict_types=1);

const SESSION_REGENERATE_INTERVAL = 1800;
const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW_SECONDS = 300;

function configure_runtime_security(): void
{
    ini_set('display_errors', '0');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
}

function is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    if (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        return true;
    }

    return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-site');
    header('Permissions-Policy: camera=(), geolocation=(), microphone=()');
    header('Content-Security-Policy: default-src \'self\'; style-src \'self\' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src \'self\' https://cdn.jsdelivr.net https://fonts.gstatic.com; img-src \'self\' data:; script-src \'none\'; object-src \'none\'; base-uri \'self\'; frame-ancestors \'none\'; form-action \'self\'');

    if (is_https_request()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function send_no_cache_headers(): void
{
    if (headers_sent()) {
        return;
    }

    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function start_app_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        $secureCookie = is_https_request();
        session_name('SHOPSESSID');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secureCookie,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        if (!isset($_SESSION['session_started_at'])) {
            session_regenerate_id(true);
            $_SESSION['session_started_at'] = time();
        } elseif (time() - (int)$_SESSION['session_started_at'] > SESSION_REGENERATE_INTERVAL) {
            session_regenerate_id(true);
            $_SESSION['session_started_at'] = time();
        }
    }
}

function is_authenticated(): bool
{
    return isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['email']);
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = (string)$user['username'];
    $_SESSION['email'] = (string)$user['email'];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function app_base_path(): string
{
    if (defined('APP_BASE_PATH')) {
        return (string)APP_BASE_PATH;
    }

    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));

    if ($scriptName === '') {
        return '/';
    }

    if (str_ends_with($scriptName, '/public/index.php')) {
        $basePath = substr($scriptName, 0, -strlen('/public/index.php'));
    } elseif (str_ends_with($scriptName, '/index.php')) {
        $basePath = substr($scriptName, 0, -strlen('/index.php'));
    } else {
        $basePath = dirname($scriptName);
    }

    $basePath = rtrim((string)$basePath, '/');

    return $basePath === '' ? '/' : $basePath;
}

function app_url(string $path = '/'): string
{
    $basePath = app_base_path();
    $normalized = '/' . ltrim($path, '/');

    if ($path === '' || $path === '/') {
        return $basePath === '/' ? '/' : $basePath . '/';
    }

    return $basePath === '/' ? $normalized : $basePath . $normalized;
}

function redirect_to(string $path): void
{
    header('Location: ' . app_url($path));
    exit;
}

function is_post_request(): bool
{
    return (string)($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string)$_SESSION['csrf_token'];
}

function is_valid_csrf_token(?string $token): bool
{
    if (!is_string($token) || $token === '' || !isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals((string)$_SESSION['csrf_token'], $token);
}

function get_recent_login_attempts(): array
{
    $attempts = $_SESSION['login_attempts'] ?? [];

    if (!is_array($attempts)) {
        return [];
    }

    $now = time();
    $filteredAttempts = [];

    foreach ($attempts as $attempt) {
        $timestamp = (int)$attempt;
        if ($timestamp > 0 && ($now - $timestamp) < LOGIN_WINDOW_SECONDS) {
            $filteredAttempts[] = $timestamp;
        }
    }

    $_SESSION['login_attempts'] = $filteredAttempts;

    return $filteredAttempts;
}

function is_login_rate_limited(): bool
{
    return count(get_recent_login_attempts()) >= LOGIN_MAX_ATTEMPTS;
}

function login_retry_after_seconds(): int
{
    $attempts = get_recent_login_attempts();

    if (count($attempts) < LOGIN_MAX_ATTEMPTS) {
        return 0;
    }

    $oldestAttempt = min($attempts);
    $retryAfter = LOGIN_WINDOW_SECONDS - (time() - $oldestAttempt);

    return max(0, $retryAfter);
}

function register_login_attempt(bool $success): void
{
    if ($success) {
        unset($_SESSION['login_attempts']);
        return;
    }

    $attempts = get_recent_login_attempts();
    $attempts[] = time();
    $_SESSION['login_attempts'] = $attempts;
}

