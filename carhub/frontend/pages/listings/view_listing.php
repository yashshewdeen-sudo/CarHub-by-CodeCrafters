<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/config/db_connect.php';
require_once __DIR__ . '/../../../backend/validation/validator.php';
start_secure_session();

$listing_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($listing_id <= 0) { http_response_code(400); die("Invalid listing."); }

$stmt = $pdo->prepare("
    SELECT cl.*, u.name AS seller_name
    FROM car_listings cl
    JOIN users u ON cl.seller_id = u.user_id
    WHERE cl.listing_id = ?
");
$stmt->execute([$listing_id]);
$car = $stmt->fetch();
if (!$car) { http_response_code(404); die("Listing not found."); }

$imgs = $pdo->prepare("SELECT file_path FROM car_images WHERE listing_id = ? ORDER BY is_main DESC, image_id ASC");
$imgs->execute([$listing_id]);
$images = $imgs->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - <?= e($car['make'].' '.$car['model']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content">
    <div class="row">
        <div class="col-md-8">
            <?php if ($car['status'] === 'Sold'): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="fas fa-ban fa-lg"></i>
                    <strong>This car has been sold.</strong> It is shown for reference only. Contact the seller if you have questions.
                </div>
            <?php endif; ?>
            <?php if (!empty($images)): ?>
                <img src="/carhub/<?= e($images[0]['file_path']) ?>" class="img-fluid rounded mb-3" style="width:100%; max-height:480px; object-fit:cover;" alt="<?= e($car['make']) ?>">
                <div class="row g-2">
                    <?php foreach (array_slice($images, 1, 4) as $img): ?>
                        <div class="col-6 col-md-3">
                            <img src="/carhub/<?= e($img['file_path']) ?>" class="img-fluid rounded" alt="">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h4 class="mt-4">Description</h4>
            <p><?= nl2br(e($car['description'] ?? 'No description provided.')) ?></p>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary fw-bold">Rs <?= number_format((float)$car['price']) ?></h2>
                    <h4 class="card-title"><?= e($car['year'].' '.$car['make'].' '.$car['model']) ?></h4>
                    <hr>
                    <table class="table table-borderless">
                        <tr><td><i class="fas fa-calendar"></i> Year</td><td class="text-end"><strong><?= (int)$car['year'] ?></strong></td></tr>
                        <tr><td><i class="fas fa-tachometer-alt"></i> Mileage</td><td class="text-end"><strong><?= number_format((int)$car['mileage']) ?> km</strong></td></tr>
                        <tr><td><i class="fas fa-gas-pump"></i> Fuel</td><td class="text-end"><strong><?= e($car['fuel_type']) ?></strong></td></tr>
                        <tr><td><i class="fas fa-cogs"></i> Transmission</td><td class="text-end"><strong><?= e($car['transmission']) ?></strong></td></tr>
                        <tr><td><i class="fas fa-car"></i> Condition</td><td class="text-end"><strong><?= e($car['condition_status']) ?></strong></td></tr>
                    </table>
                    <hr>
                    <div class="d-grid gap-2">
                        <?php if ($car['status'] === 'Sold'): ?>
                            <button class="btn btn-secondary btn-lg" disabled><i class="fas fa-ban"></i> Sold</button>
                        <?php else: ?>
                            <a href="/carhub/frontend/pages/listings/contact.php?id=<?= $listing_id ?>" class="btn btn-primary btn-lg">Contact Seller</a>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted text-center mt-2 small">Sold by: <strong><?= e($car['seller_name']) ?></strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
