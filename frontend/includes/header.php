<?php
// frontend/includes/header.php
require_once __DIR__ . '/../../backend/security/session_security.php';
require_once __DIR__ . '/../../backend/security/csrf.php';
start_secure_session();
check_session_timeout();

// Unread message count for logged-in users
$_unread_count = 0;
if (is_logged_in()) {
    try {
        require_once __DIR__ . '/../../backend/config/db_connect.php';
        $s = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
        $s->execute([$_SESSION['user_id']]);
        $_unread_count = (int)$s->fetchColumn();
    } catch (Throwable $e) { $_unread_count = 0; }
}
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/carhub/index.php"><i class="fas fa-car-side"></i> CarHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/carhub/index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/carhub/frontend/pages/listings/browse_listings.php">Browse Cars</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (in_array($_SESSION['role'] ?? '', ['Seller','Admin'], true)): ?>
                        <li class="nav-item"><a class="nav-link" href="/carhub/frontend/pages/listings/create_listing.php">Sell Your Car</a></li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-semibold"
                               href="/carhub/frontend/pages/dashboard/account.php#upgrade"
                               title="Upgrade to Seller to post listings">
                                <i class="fas fa-store"></i> Become a Seller
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (is_logged_in()): ?>
                    <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="/carhub/frontend/pages/dashboard/admin_dashboard.php">Admin</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/carhub/frontend/pages/dashboard/seller_dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/carhub/frontend/pages/dashboard/inbox.php">
                            <i class="fas fa-inbox"></i> Inbox
                            <?php if ($_unread_count > 0): ?>
                                <span class="badge bg-danger"><?= $_unread_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link text-danger" href="/carhub/logout.php">Logout</a></li>
                    <li class="nav-item"><a class="nav-link" href="/carhub/frontend/pages/dashboard/account.php"><i class="fas fa-user-circle"></i> Account</a></li>
                <?php else: ?>
                    <li class="nav-item ms-2"><a class="btn btn-primary" href="/carhub/frontend/pages/auth/login.php">Login / Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
