<?php
// backend/api/consume_listings.php
// Demonstrates server-side consumption + validation of a JSON file against its schema.

require_once __DIR__ . '/../validation/schema_validator.php';
header('Content-Type: application/json; charset=utf-8');

$json_file   = __DIR__ . '/../../data/listings.json';
$schema_file = __DIR__ . '/../schemas/listings.schema.json';

if (!file_exists($json_file)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'data/listings.json not found. Call export_listings.php first.']);
    exit();
}

$raw  = file_get_contents($json_file);
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON in listings.json']);
    exit();
}

$result = validate_json_against_schema($data, $schema_file);

echo json_encode([
    'success'        => $result['valid'],
    'validated_with' => 'listings.schema.json',
    'errors'         => $result['errors'],
    'count'          => $data['count'] ?? 0,
    'sample'         => array_slice($data['listings'] ?? [], 0, 3),
], JSON_PRETTY_PRINT);
