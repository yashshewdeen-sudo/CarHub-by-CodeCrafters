<?php
// backend/security/session_security.php

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        $params = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $params['lifetime'],
            'path'     => '/',
            'domain'   => $params['domain'],
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        session_start();
    }
}

function regenerate_session() {
    session_regenerate_id(true);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login($redirect = '/carhub/frontend/pages/auth/login.php') {
    if (!is_logged_in()) {
        header("Location: $redirect");
        exit();
    }
}

function require_role($roles, $redirect = '/carhub/index.php') {
    if (!is_array($roles)) $roles = [$roles];
    if (!is_logged_in() || !in_array($_SESSION['role'] ?? '', $roles, true)) {
        header("Location: $redirect");
        exit();
    }
}

function check_session_timeout() {
    $timeout = 30 * 60;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}
