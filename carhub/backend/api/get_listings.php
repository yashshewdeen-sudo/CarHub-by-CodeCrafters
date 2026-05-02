<?php
// backend/api/get_listings.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // demo only
require_once __DIR__ . '/../config/db_connect.php';

$search    = trim($_GET['search']    ?? '');
$min_price = is_numeric($_GET['min_price'] ?? null) ? (float)$_GET['min_price'] : 0;
$max_price = is_numeric($_GET['max_price'] ?? null) ? (float)$_GET['max_price'] : 99999999;
$fuel      = trim($_GET['fuel']      ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$per_page  = min(50, max(1, (int)($_GET['per_page'] ?? 12)));
$offset    = ($page - 1) * $per_page;

$params = [];
$allowed_statuses = ['Active', 'Sold', 'Pending', 'Rejected'];
$status_param     = trim($_GET['status'] ?? '');
$public_param     = trim($_GET['public'] ?? '');  // public=1 → Active+Sold for browse page

if (in_array($status_param, $allowed_statuses, true)) {
    // Explicit single status (e.g. home page requests Active only)
    $where    = "cl.status = ?";
    $params[] = $status_param;
} elseif ($public_param === '1') {
    // Browse page: show Active and Sold, hide Pending/Rejected
    $where = "cl.status IN ('Active','Sold')";
} else {
    // Default fallback: Active only (home page carousel)
    $where = "cl.status = 'Active'";
}

if ($search !== '') {
    $where .= " AND (cl.make LIKE ? OR cl.model LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($min_price > 0)         { $where .= " AND cl.price >= ?"; $params[] = $min_price; }
if ($max_price < 99999999)  { $where .= " AND cl.price <= ?"; $params[] = $max_price; }
if ($fuel !== '')           { $where .= " AND cl.fuel_type = ?"; $params[] = $fuel; }

// total count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM car_listings cl WHERE $where");
$count_stmt->execute($params);
$total = (int)$count_stmt->fetchColumn();

$sql = "SELECT cl.*, ci.file_path AS main_image
        FROM car_listings cl
        LEFT JOIN car_images ci ON cl.listing_id = ci.listing_id AND ci.is_main = 1
        WHERE $where
        ORDER BY cl.created_at DESC
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();

echo json_encode([
    'page'      => $page,
    'per_page'  => $per_page,
    'total'     => $total,
    'total_pages' => (int)ceil($total / $per_page),
    'listings'  => $listings,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);