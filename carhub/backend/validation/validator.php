<?php
// backend/validation/validator.php
// Note: clean_input() does NOT htmlspecialchars on storage; we escape on output.

function clean_input($data) {
    if ($data === null) return '';
    $data = trim((string)$data);
    $data = stripslashes($data);
    return $data;
}

function e($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_password_strength($password) {
    return strlen($password) >= 8
        && preg_match('/[A-Za-z]/', $password)
        && preg_match('/[0-9]/', $password);
}

function validate_phone($phone) {
    return (bool)preg_match('/^[0-9+\s\-]{8,15}$/', $phone);
}
