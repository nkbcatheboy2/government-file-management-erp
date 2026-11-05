<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strtolower($_SERVER['HTTPS']) !== 'off';
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $isHttps,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function require_login()
{
    $lastActivity = $_SESSION['last_activity'] ?? 0;
    if (!isset($_SESSION['authenticated']) || time() - $lastActivity > 3600) {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: login.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function app_auth_config()
{
    static $authConfig = null;
    if ($authConfig !== null) {
        return $authConfig;
    }

    $configFile = __DIR__ . '/config.php';
    if (!is_file($configFile) || !is_readable($configFile)) {
        return null;
    }

    $config = require $configFile;
    $authConfig = $config['auth'] ?? null;
    if (!is_array($authConfig)
        || !isset($authConfig['username'], $authConfig['password_hash'])
        || !is_string($authConfig['username'])
        || !is_string($authConfig['password_hash'])
        || $authConfig['username'] === ''
        || $authConfig['password_hash'] === ''
        || !password_get_info($authConfig['password_hash'])['algo']) {
        return null;
    }

    return $authConfig;
}
?>
