<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
require_once __DIR__ . '/../../../backend/config/db_connect.php';
require_once __DIR__ . '/../../../backend/validation/validator.php';
start_secure_session();

$listing_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($listing_id <= 0) die("Invalid listing.");

$stmt = $pdo->prepare("SELECT cl.*, u.name AS seller_name FROM car_listings cl JOIN users u ON cl.seller_id = u.user_id WHERE cl.listing_id = ?");
$stmt->execute([$listing_id]);
$car = $stmt->fetch();
if (!$car) die("Listing not found.");

if (!is_logged_in()) {
    $_SESSION['login_error'] = "Please log in to contact a seller.";
    header("Location: /carhub/frontend/pages/auth/login.php");
    exit();
}

$csrf = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Contact Seller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body class="bg-light">
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content" style="max-width:800px;">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white p-4">
            <h3 class="mb-0"><i class="fas fa-envelope"></i> Contact Seller</h3>
            <p class="mb-0 opacity-75">About: <strong><?= e($car['year'].' '.$car['make'].' '.$car['model']) ?></strong></p>
        </div>
        <div class="card-body p-4">
            <?php
            if (!empty($_SESSION['message_success'])) {
                echo '<div class="alert alert-success">' . e($_SESSION['message_success']) . '</div>';
                unset($_SESSION['message_success']);
            }
            if (!empty($_SESSION['message_error'])) {
                echo '<div class="alert alert-danger">' . e($_SESSION['message_error']) . '</div>';
                unset($_SESSION['message_error']);
            }
            ?>
            <form action="/carhub/backend/controllers/send_message.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="hidden" name="listing_id" value="<?= $listing_id ?>">
                <div class="mb-3">
                    <label class="form-label fw-bold">Vehicle of Interest</label>
                    <input type="text" class="form-control bg-light" value="<?= e($car['year'].' '.$car['make'].' '.$car['model']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Message</label>
                    <textarea class="form-control" name="message" rows="5" maxlength="2000" required
                              placeholder="Hi, is this car still available?"></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/carhub/frontend/pages/listings/view_listing.php?id=<?= $listing_id ?>" class="text-muted text-decoration-none"><i class="fas fa-arrow-left"></i> Back</a>
                    <button class="btn btn-primary btn-lg px-5">Send Message <i class="fas fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
