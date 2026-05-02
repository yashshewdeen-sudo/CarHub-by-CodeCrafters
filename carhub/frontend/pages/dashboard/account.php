<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
require_once __DIR__ . '/../../../backend/config/db_connect.php';
require_once __DIR__ . '/../../../backend/validation/validator.php';
start_secure_session();
require_login();

$csrf    = generate_csrf_token();
$user_id = (int)$_SESSION['user_id'];

// Fetch fresh user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$success = '';
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid form submission.";
    } else {
        $action = $_POST['action'] ?? '';

        // ── 1. Update profile ──────────────────────────────────────────
        if ($action === 'update_profile') {
            $name  = clean_input($_POST['name']  ?? '');
            $phone = clean_input($_POST['phone'] ?? '');

            if ($name === '' || strlen($name) < 2)   $errors[] = "Name must be at least 2 characters.";
            if (!validate_phone($phone))              $errors[] = "Valid phone number required.";

            if (empty($errors)) {
                $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE user_id = ?")
                    ->execute([$name, $phone, $user_id]);
                $_SESSION['name'] = $name;
                $success = "Profile updated successfully.";
                // Refresh
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            }
        }

        // ── 2. Change password ─────────────────────────────────────────
        if ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new_pw  = $_POST['new_password']     ?? '';
            $confirm = $_POST['confirm_password']  ?? '';

            if (!password_verify($current, $user['password']))   $errors[] = "Current password is incorrect.";
            if (!validate_password_strength($new_pw))            $errors[] = "New password must be at least 8 chars with letters and numbers.";
            if ($new_pw !== $confirm)                            $errors[] = "New passwords do not match.";

            if (empty($errors)) {
                $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?")
                    ->execute([password_hash($new_pw, PASSWORD_DEFAULT), $user_id]);
                $success = "Password changed successfully.";
            }
        }

        // ── 3. Upgrade to Seller ───────────────────────────────────────
        if ($action === 'upgrade_to_seller') {
            if ($user['role'] !== 'Buyer') {
                $errors[] = "Only Buyer accounts can upgrade to Seller.";
            } else {
                $pdo->prepare("UPDATE users SET role = 'Seller' WHERE user_id = ?")
                    ->execute([$user_id]);
                $_SESSION['role'] = 'Seller';
                $success = "Your account has been upgraded to Seller. You can now post listings!";
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body class="bg-light">
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content" style="max-width:720px;">
    <h2 class="mb-4"><i class="fas fa-user-circle"></i> My Account</h2>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0"><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
        </div>
    <?php endif; ?>

    <!-- Role badge -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <i class="fas fa-id-badge fa-2x text-primary"></i>
            <div>
                <div class="fw-bold fs-5"><?= htmlspecialchars($user['name']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($user['email']) ?></div>
                <span class="badge bg-<?= $user['role']==='Admin'?'danger':($user['role']==='Seller'?'success':'secondary') ?> mt-1">
                    <?= htmlspecialchars($user['role']) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- ── Upgrade to Seller ── (only shown to Buyers) -->
    <?php if ($user['role'] === 'Buyer'): ?>
    <div class="card mb-4 border-warning shadow-sm" id="upgrade">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-store text-warning"></i> Want to Sell a Car?</h5>
            <p class="text-muted mb-3">
                Upgrade your account to <strong>Seller</strong> to post car listings, manage your inventory,
                and receive messages from buyers. It's free and instant.
            </p>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action"     value="upgrade_to_seller">
                <button class="btn btn-warning fw-bold" onclick="return confirm('Upgrade your account to Seller?')">
                    <i class="fas fa-arrow-up"></i> Upgrade to Seller
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ── Edit Profile ── -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-user-edit text-primary"></i> Edit Profile
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action"     value="update_profile">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= htmlspecialchars($user['name']) ?>" required minlength="2" maxlength="100">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-muted small">(cannot be changed)</span></label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control"
                           value="<?= htmlspecialchars($user['phone']) ?>" required pattern="[+0-9\s\-]{8,15}">
                </div>
                <button class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>
    </div>

    <!-- ── Change Password ── -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="fas fa-lock text-primary"></i> Change Password
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="action"     value="change_password">
                <div class="mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <span class="text-muted small">(min 8 chars, letters + numbers)</span></label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="8">
                </div>
                <button class="btn btn-outline-primary"><i class="fas fa-key"></i> Change Password</button>
            </form>
        </div>
    </div>

    <!-- ── Account Info ── -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-muted small">
            <strong>Member since:</strong> <?= date('d F Y', strtotime($user['created_at'])) ?><br>
            <strong>Last login:</strong> <?= $user['last_login'] ? date('d F Y, H:i', strtotime($user['last_login'])) : 'N/A' ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
if (location.hash === '#upgrade') {
    const card = document.getElementById('upgrade');
    if (card) {
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        card.style.transition = 'box-shadow .3s';
        card.style.boxShadow  = '0 0 0 4px rgba(255,193,7,.6)';
        setTimeout(() => card.style.boxShadow = '', 2000);
    }
}
</script>
</html>
