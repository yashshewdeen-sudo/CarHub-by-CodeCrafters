<?php
// backend/controllers/send_message.php
require_once __DIR__ . '/../security/session_security.php';
require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../validation/validator.php';

start_secure_session();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /carhub/frontend/pages/listings/browse_listings.php');
    exit();
}

$listing_id = (int)($_POST['listing_id'] ?? 0);
$back = "/carhub/frontend/pages/listings/contact.php?id=$listing_id";

if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['message_error'] = "Invalid form submission.";
    header("Location: $back");
    exit();
}

$message_body = clean_input($_POST['message'] ?? '');
if ($message_body === '' || strlen($message_body) > 2000) {
    $_SESSION['message_error'] = "Message is required (max 2000 chars).";
    header("Location: $back");
    exit();
}

$stmt = $pdo->prepare("SELECT seller_id FROM car_listings WHERE listing_id = ?");
$stmt->execute([$listing_id]);
$listing = $stmt->fetch();
if (!$listing) {
    $_SESSION['message_error'] = "Listing not found.";
    header("Location: /carhub/frontend/pages/listings/browse_listings.php");
    exit();
}

if ((int)$listing['seller_id'] === (int)$_SESSION['user_id']) {
    $_SESSION['message_error'] = "You cannot message your own listing.";
    header("Location: $back");
    exit();
}

$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, listing_id, message_body) VALUES (?,?,?,?)");
$stmt->execute([$_SESSION['user_id'], $listing['seller_id'], $listing_id, $message_body]);

$_SESSION['message_success'] = "Message sent.";
header("Location: $back");
exit();
