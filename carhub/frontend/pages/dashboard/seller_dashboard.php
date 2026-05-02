<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
require_once __DIR__ . '/../../../backend/config/db_connect.php';
require_once __DIR__ . '/../../../backend/validation/validator.php';
start_secure_session();
require_login();
require_role(['Seller','Admin']);

$csrf = generate_csrf_token();
$stmt = $pdo->prepare("
    SELECT cl.*, ci.file_path AS main_image
    FROM car_listings cl
    LEFT JOIN car_images ci ON cl.listing_id = ci.listing_id AND ci.is_main = 1
    WHERE cl.seller_id = ?
    ORDER BY cl.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$listings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - My Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Car Listings</h2>
        <a href="/carhub/frontend/pages/listings/create_listing.php" class="btn btn-success">+ Post New Listing</a>
    </div>

    <?php if (empty($listings)): ?>
        <div class="alert alert-info">No listings yet.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr>
                    <th>Image</th><th>Car</th><th>Price</th><th>Status</th><th>Created</th><th>Actions</th>
                </tr></thead>
                <tbody>
                <?php foreach ($listings as $l): ?>
                    <tr id="row-<?= (int)$l['listing_id'] ?>">
                        <td><?php if ($l['main_image']): ?>
                            <img src="/carhub/<?= e($l['main_image']) ?>" width="80" height="60" style="object-fit:cover;">
                        <?php endif; ?></td>
                        <td><?= e($l['year'].' '.$l['make'].' '.$l['model']) ?></td>
                        <td>Rs <?= number_format((float)$l['price'], 2) ?></td>
                        <td>
                            <span class="badge bg-<?= $l['status']==='Active'?'success':($l['status']==='Pending'?'warning':'secondary') ?>">
                                <?= e($l['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($l['created_at'])) ?></td>
                        <td>
                            <a href="/carhub/frontend/pages/listings/view_listing.php?id=<?= (int)$l['listing_id'] ?>" class="btn btn-sm btn-primary">View</a>
                            <button onclick="updateStatus(<?= (int)$l['listing_id'] ?>, 'Sold')"   class="btn btn-sm btn-success">Mark Sold</button>
                            <button onclick="deleteListing(<?= (int)$l['listing_id'] ?>)"          class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = '<?= e($csrf) ?>';
function updateStatus(id, status) {
    if (!confirm('Mark listing as ' + status + '?')) return;
    $.post('/carhub/backend/controllers/update_listing_status.php',
        { listing_id: id, status: status, csrf_token: CSRF },
        function (res) {
            alert(res.message);
            if (res.success) location.reload();
        }, 'json'
    ).fail(() => alert('Network error.'));
}
function deleteListing(id) {
    if (!confirm('Delete this listing? This cannot be undone.')) return;
    $.post('/carhub/backend/controllers/delete_listing.php',
        { listing_id: id, csrf_token: CSRF },
        function (res) {
            alert(res.message);
            if (res.success) $('#row-' + id).fadeOut(300, function () { $(this).remove(); });
        }, 'json'
    ).fail(() => alert('Network error.'));
}
</script>
</body>
</html>
