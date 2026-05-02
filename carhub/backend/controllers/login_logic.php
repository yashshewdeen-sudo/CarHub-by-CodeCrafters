<?php
// backend/controllers/login_logic.php
require_once __DIR__ . '/../security/session_security.php';
require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../validation/validator.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /carhub/frontend/pages/auth/login.php');
    exit();
}

if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['login_error'] = "Invalid form submission.";
    header("Location: /carhub/frontend/pages/auth/login.php");
    exit();
}

$email    = clean_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: /carhub/frontend/pages/auth/login.php");
    exit();
}

regenerate_session();
$_SESSION['user_id']       = $user['user_id'];
$_SESSION['name']          = $user['name'];
$_SESSION['role']          = $user['role'];
$_SESSION['last_activity'] = time();

$pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?")
    ->execute([$user['user_id']]);

$dest = match($user['role']) {
    'Admin'  => '/carhub/frontend/pages/dashboard/admin_dashboard.php',
    'Seller' => '/carhub/frontend/pages/dashboard/seller_dashboard.php',
    default  => '/carhub/frontend/pages/dashboard/account.php',
};
header("Location: $dest");
exit();
