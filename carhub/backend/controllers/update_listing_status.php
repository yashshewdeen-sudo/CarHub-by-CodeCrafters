<?php
// backend/controllers/update_listing_status.php
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
$new_status = $_POST['status'] ?? '';

$allowed = ['Active','Sold','Pending','Rejected'];
if (!in_array($new_status, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']); exit();
}

// Sellers cannot set status back to Pending — admin only
if (($_SESSION['role'] ?? '') !== 'Admin' && $new_status === 'Pending') {
    echo json_encode(['success' => false, 'message' => 'Only admin can set Pending status.']); exit();
}

// Admin can update any; sellers only their own
if (($_SESSION['role'] ?? '') === 'Admin') {
    $stmt = $pdo->prepare("UPDATE car_listings SET status = ? WHERE listing_id = ?");
    $ok = $stmt->execute([$new_status, $listing_id]);
} else {
    $stmt = $pdo->prepare("UPDATE car_listings SET status = ? WHERE listing_id = ? AND seller_id = ?");
    $ok = $stmt->execute([$new_status, $listing_id, $_SESSION['user_id']]);
}

echo json_encode([
    'success' => $ok && $stmt->rowCount() > 0,
    'message' => ($ok && $stmt->rowCount() > 0) ? 'Status updated.' : 'No change made.',
]);
