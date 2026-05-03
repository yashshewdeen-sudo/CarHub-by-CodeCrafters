<?php
// backend/api/export_listings.php
// Produces data/listings.json and validates it against listings.schema.json
// at creation time. Returns the validation result as JSON.

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../validation/schema_validator.php';

header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->query("
    SELECT cl.listing_id, cl.seller_id, cl.make, cl.model, cl.year, cl.mileage,
           cl.price, cl.fuel_type, cl.transmission, cl.condition_status,
           cl.description, cl.status,
           ci.file_path AS main_image
    FROM car_listings cl
    LEFT JOIN car_images ci ON cl.listing_id = ci.listing_id AND ci.is_main = 1
    ORDER BY cl.listing_id ASC
");
$rows = $stmt->fetchAll();

// Cast numeric columns so the schema's integer/number types match
foreach ($rows as &$r) {
    $r['listing_id'] = (int)$r['listing_id'];
    $r['seller_id']  = (int)$r['seller_id'];
    $r['year']       = (int)$r['year'];
    $r['mileage']    = (int)$r['mileage'];
    $r['price']      = (float)$r['price'];
}
unset($r);

$payload = [
    'generated_at' => date('c'),
    'count'        => count($rows),
    'listings'     => $rows,
];

$schema_file = __DIR__ . '/../schemas/listings.schema.json';
$result = validate_json_against_schema($payload, $schema_file);

if (!$result['valid']) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Validation failed at creation time. File NOT written.',
        'errors'  => $result['errors'],
    ], JSON_PRETTY_PRINT);
    exit();
}

$out_dir  = __DIR__ . '/../../data';
if (!is_dir($out_dir)) mkdir($out_dir, 0755, true);
$out_file = $out_dir . '/listings.json';
file_put_contents($out_file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo json_encode([
    'success' => true,
    'message' => 'listings.json written and validated against schema.',
    'count'   => count($rows),
    'path'    => 'data/listings.json',
], JSON_PRETTY_PRINT);
