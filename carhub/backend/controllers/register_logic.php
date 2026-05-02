<?php
// backend/controllers/register_logic.php
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
    $_SESSION['register_errors'] = ["Invalid form submission."];
    header("Location: /carhub/frontend/pages/auth/login.php#register");
    exit();
}

$name     = clean_input($_POST['name']  ?? '');
$email    = clean_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$phone    = clean_input($_POST['phone'] ?? '');
$role     = clean_input($_POST['role']  ?? 'Buyer');

$errors = [];
if ($name === '')                            $errors[] = "Full name is required.";
if (!validate_email($email))                 $errors[] = "Invalid email format.";
if (!validate_password_strength($password))  $errors[] = "Password must be at least 8 chars with letters and numbers.";
if (!validate_phone($phone))                 $errors[] = "Valid phone number required.";

// Public signup restricted to Buyer/Seller. Admin must be promoted in DB.
$allowed_roles = ['Buyer', 'Seller'];
if (!in_array($role, $allowed_roles, true)) $errors[] = "Invalid role.";

if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email already registered.";
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    header("Location: /carhub/frontend/pages/auth/login.php#register");
    exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)");
$stmt->execute([$name, $email, $hash, $phone, $role]);

$_SESSION['success'] = "Registration successful! Please login.";
header("Location: /carhub/frontend/pages/auth/login.php");
exit();
