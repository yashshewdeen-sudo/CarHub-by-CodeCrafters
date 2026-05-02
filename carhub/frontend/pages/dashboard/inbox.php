<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
require_once __DIR__ . '/../../../backend/config/db_connect.php';
require_once __DIR__ . '/../../../backend/validation/validator.php';
start_secure_session();
require_login();

$csrf = generate_csrf_token();
$user_id = (int)$_SESSION['user_id'];

// Mark a message as read via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    if (validate_csrf_token($_POST['csrf_token'] ?? '')) {
        $mid = (int)($_POST['message_id'] ?? 0);
        $pdo->prepare("UPDATE messages SET is_read = 1 WHERE message_id = ? AND receiver_id = ?")
            ->execute([$mid, $user_id]);
    }
    header("Location: /carhub/frontend/pages/dashboard/inbox.php");
    exit();
}

// Received messages
$received = $pdo->prepare("
    SELECT m.*, u.name AS sender_name,
           cl.make, cl.model, cl.year, cl.listing_id
    FROM messages m
    JOIN users u         ON m.sender_id  = u.user_id
    JOIN car_listings cl ON m.listing_id = cl.listing_id
    WHERE m.receiver_id = ?
    ORDER BY m.created_at DESC
");
$received->execute([$user_id]);
$received_msgs = $received->fetchAll();

// Sent messages
$sent = $pdo->prepare("
    SELECT m.*, u.name AS receiver_name,
           cl.make, cl.model, cl.year, cl.listing_id
    FROM messages m
    JOIN users u         ON m.receiver_id = u.user_id
    JOIN car_listings cl ON m.listing_id  = cl.listing_id
    WHERE m.sender_id = ?
    ORDER BY m.created_at DESC
");
$sent->execute([$user_id]);
$sent_msgs = $sent->fetchAll();

$unread_count = count(array_filter($received_msgs, fn($m) => !$m['is_read']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Inbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-inbox"></i> Inbox
            <?php if ($unread_count > 0): ?>
                <span class="badge bg-danger"><?= $unread_count ?> unread</span>
            <?php endif; ?>
        </h2>
        <a href="/carhub/frontend/pages/dashboard/seller_dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Dashboard
        </a>
    </div>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#received">
                <i class="fas fa-envelope"></i> Received
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger"><?= $unread_count ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#sent">
                <i class="fas fa-paper-plane"></i> Sent
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- RECEIVED -->
        <div class="tab-pane fade show active" id="received">
            <?php if (empty($received_msgs)): ?>
                <div class="alert alert-info">No messages received yet.</div>
            <?php else: ?>
                <?php foreach ($received_msgs as $msg): ?>
                    <div class="card mb-3 <?= !$msg['is_read'] ? 'border-primary' : 'border-0' ?> shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <?php if (!$msg['is_read']): ?>
                                        <span class="badge bg-primary me-2">New</span>
                                    <?php endif; ?>
                                    <strong><?= e($msg['sender_name']) ?></strong>
                                    <span class="text-muted"> about </span>
                                    <a href="/carhub/frontend/pages/listings/view_listing.php?id=<?= (int)$msg['listing_id'] ?>">
                                        <?= e($msg['year'].' '.$msg['make'].' '.$msg['model']) ?>
                                    </a>
                                </div>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></small>
                            </div>
                            <p class="mt-3 mb-2"><?= nl2br(e($msg['message_body'])) ?></p>
                            <?php if (!$msg['is_read']): ?>
                                <form method="POST">
                                    <input type="hidden" name="csrf_token"  value="<?= e($csrf) ?>">
                                    <input type="hidden" name="message_id" value="<?= (int)$msg['message_id'] ?>">
                                    <input type="hidden" name="mark_read"  value="1">
                                    <button class="btn btn-sm btn-outline-primary">Mark as Read</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SENT -->
        <div class="tab-pane fade" id="sent">
            <?php if (empty($sent_msgs)): ?>
                <div class="alert alert-info">No messages sent yet.</div>
            <?php else: ?>
                <?php foreach ($sent_msgs as $msg): ?>
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="text-muted">To: </span>
                                    <strong><?= e($msg['receiver_name']) ?></strong>
                                    <span class="text-muted"> about </span>
                                    <a href="/carhub/frontend/pages/listings/view_listing.php?id=<?= (int)$msg['listing_id'] ?>">
                                        <?= e($msg['year'].' '.$msg['make'].' '.$msg['model']) ?>
                                    </a>
                                    <?php if ($msg['is_read']): ?>
                                        <span class="badge bg-success ms-2">Read</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary ms-2">Unread</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted"><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?></small>
                            </div>
                            <p class="mt-3 mb-0"><?= nl2br(e($msg['message_body'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
