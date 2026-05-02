<?php
// backend/controllers/delete_listing.php
require_once __DIR__ . '/../security/session_security.php';
require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db_connect.php';

start_secure_session();
header('Content-Type: application/json');

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit();
}
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'CSRF error']); exit();
}

$listing_id = (int)($_POST['listing_id'] ?? 0);

if (($_SESSION['role'] ?? '') === 'Admin') {
    $stmt = $pdo->prepare("DELETE FROM car_listings WHERE listing_id = ?");
    $ok = $stmt->execute([$listing_id]);
} else {
    $stmt = $pdo->prepare("DELETE FROM car_listings WHERE listing_id = ? AND seller_id = ?");
    $ok = $stmt->execute([$listing_id, $_SESSION['user_id']]);
}

echo json_encode([
    'success' => $ok && $stmt->rowCount() > 0,
    'message' => ($ok && $stmt->rowCount() > 0) ? 'Listing deleted.' : 'Not found or not yours.',
]);
