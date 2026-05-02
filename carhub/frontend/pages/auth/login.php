<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
start_secure_session();
$csrf = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Login / Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
    <script defer src="/carhub/assets/js/validation.js"></script>
    <style>.auth-container{max-width:480px;margin:120px auto;}</style>
</head>
<body class="bg-light">
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container auth-container">
    <div class="card shadow">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#login" role="tab">Login</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#register" role="tab">Register</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">

                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <?php
                    if (!empty($_SESSION['login_error'])) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                        unset($_SESSION['login_error']);
                    }
                    if (!empty($_SESSION['success'])) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                        unset($_SESSION['success']);
                    }
                    ?>
                    <form id="loginForm" action="/carhub/backend/controllers/login_logic.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <div class="mb-3"><label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8"></div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                </div>

                <div class="tab-pane fade" id="register" role="tabpanel">
                    <?php
                    if (!empty($_SESSION['register_errors'])) {
                        echo '<div class="alert alert-danger"><ul>';
                        foreach ($_SESSION['register_errors'] as $err) echo '<li>' . htmlspecialchars($err) . '</li>';
                        echo '</ul></div>';
                        unset($_SESSION['register_errors']);
                    }
                    ?>
                    <form id="registerForm" action="/carhub/backend/controllers/register_logic.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <div class="mb-3"><label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required minlength="3" maxlength="100"></div>
                        <div class="mb-3"><label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" required pattern="[0-9+\s\-]{8,15}"></div>
                        <div class="mb-3"><label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8"></div>
                        <div class="mb-3"><label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8"></div>
                        <div class="mb-3"><label class="form-label">I want to:</label>
                            <select name="role" class="form-select">
                                <option value="Buyer">Buy Cars</option>
                                <option value="Seller">Sell Cars</option>
                            </select></div>
                        <button class="btn btn-success w-100">Create Account</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// open #register tab if URL has hash
if (location.hash === '#register') {
    new bootstrap.Tab(document.querySelector('a[href="#register"]')).show();
}
</script>
</body>
</html>
