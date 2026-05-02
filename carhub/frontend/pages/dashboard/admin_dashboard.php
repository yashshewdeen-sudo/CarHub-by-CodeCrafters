<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
require_once __DIR__ . '/../../../backend/config/db_connect.php';
require_once __DIR__ . '/../../../backend/validation/validator.php';
start_secure_session();
require_role('Admin');

$csrf = generate_csrf_token();
$pending = $pdo->query("SELECT cl.*, u.name AS seller_name FROM car_listings cl JOIN users u ON cl.seller_id = u.user_id WHERE cl.status = 'Pending' ORDER BY cl.created_at DESC")->fetchAll();
$users   = $pdo->query("SELECT user_id, name, email, role, is_active, created_at FROM users ORDER BY user_id ASC")->fetchAll();
$stats = [
    'users'    => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'listings' => (int)$pdo->query("SELECT COUNT(*) FROM car_listings")->fetchColumn(),
    'active'   => (int)$pdo->query("SELECT COUNT(*) FROM car_listings WHERE status='Active'")->fetchColumn(),
    'pending'  => (int)$pdo->query("SELECT COUNT(*) FROM car_listings WHERE status='Pending'")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content">
    <h2 class="mb-4"><i class="fas fa-user-shield"></i> Admin Dashboard</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card text-bg-primary"><div class="card-body"><h6>Users</h6><h2><?= $stats['users'] ?></h2></div></div></div>
        <div class="col-md-3"><div class="card text-bg-info"   ><div class="card-body"><h6>Total Listings</h6><h2><?= $stats['listings'] ?></h2></div></div></div>
        <div class="col-md-3"><div class="card text-bg-success"><div class="card-body"><h6>Active</h6><h2><?= $stats['active'] ?></h2></div></div></div>
        <div class="col-md-3"><div class="card text-bg-warning"><div class="card-body"><h6>Pending</h6><h2><?= $stats['pending'] ?></h2></div></div></div>
    </div>

    <h4 class="mt-4">JSON / Schema Tools</h4>
    <div class="mb-4">
        <button class="btn btn-outline-primary" id="btnExport">Export listings.json (validate at creation)</button>
        <button class="btn btn-outline-secondary" id="btnConsume">Consume + Validate listings.json</button>
        <pre id="jsonOut" class="bg-light p-3 mt-3" style="max-height:300px; overflow:auto;">Output appears here.</pre>
    </div>

    <h4>Pending Listings</h4>
    <?php if (empty($pending)): ?>
        <div class="alert alert-info">No pending listings.</div>
    <?php else: ?>
        <table class="table table-hover">
            <thead><tr><th>Car</th><th>Seller</th><th>Price</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($pending as $l): ?>
                <tr id="row-<?= (int)$l['listing_id'] ?>">
                    <td><?= e($l['year'].' '.$l['make'].' '.$l['model']) ?></td>
                    <td><?= e($l['seller_name']) ?></td>
                    <td>Rs <?= number_format((float)$l['price']) ?></td>
                    <td><?= date('d M Y', strtotime($l['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="setStatus(<?= (int)$l['listing_id'] ?>, 'Active')">Approve</button>
                        <button class="btn btn-sm btn-danger"  onclick="setStatus(<?= (int)$l['listing_id'] ?>, 'Rejected')">Reject</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h4 class="mt-5">Users</h4>
    <table class="table table-sm">
        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Active</th><th>Joined</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)$u['user_id'] ?></td>
                <td><?= e($u['name']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['role']) ?></td>
                <td><?= $u['is_active'] ? 'Yes' : 'No' ?></td>
                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Ajv for client-side JSON Schema validation -->
<script src="https://cdn.jsdelivr.net/npm/ajv@8.17.1/dist/ajv.min.js"></script>
<script>
const CSRF = '<?= e($csrf) ?>';

function setStatus(id, status) {
    $.post('/carhub/backend/controllers/update_listing_status.php',
        { listing_id: id, status: status, csrf_token: CSRF },
        function (res) {
            alert(res.message);
            if (res.success) $('#row-' + id).fadeOut();
        }, 'json'
    );
}

$('#btnExport').on('click', function () {
    $('#jsonOut').text('Exporting...');
    $.getJSON('/carhub/backend/api/export_listings.php', function (res) {
        $('#jsonOut').text(JSON.stringify(res, null, 2));
    }).fail(x => $('#jsonOut').text('Export failed: ' + x.responseText));
});

$('#btnConsume').on('click', function () {
    $('#jsonOut').text('Loading listings.json...');
    // 1) fetch the JSON
    $.getJSON('/carhub/data/listings.json', function (data) {
        // 2) fetch the schemas and validate client-side via Ajv
        $.when(
            $.getJSON('/carhub/backend/schemas/listings.schema.json'),
            $.getJSON('/carhub/backend/schemas/listing.schema.json')
        ).done(function (a, b) {
            const listingsSchema = a[0];
            const listingSchema  = b[0];
            const ajv = new Ajv({allErrors: true, strict: false});
            ajv.addSchema(listingSchema, 'listing.schema.json');
            const validate = ajv.compile(listingsSchema);
            const valid = validate(data);
            $('#jsonOut').text(
                'Client-side Ajv validation: ' + (valid ? 'PASS' : 'FAIL') + '\n\n' +
                (valid ? '' : JSON.stringify(validate.errors, null, 2) + '\n\n') +
                'Sample data:\n' + JSON.stringify({count: data.count, first: data.listings[0]}, null, 2)
            );
        });
    }).fail(() => $('#jsonOut').text('listings.json not found. Run Export first.'));
});
</script>
</body>
</html>
