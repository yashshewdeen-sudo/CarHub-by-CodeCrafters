<?php
// backend/validation/listing_validation.php
require_once __DIR__ . '/validator.php';

function validate_listing(array $data) : array {
    $errors = [];

    $make  = clean_input($data['make']  ?? '');
    $model = clean_input($data['model'] ?? '');
    $description = clean_input($data['description'] ?? '');

    if ($make === '' || strlen($make) > 50)   $errors[] = "Make is required (max 50 chars).";
    if ($model === '' || strlen($model) > 50) $errors[] = "Model is required (max 50 chars).";

    $current_year = (int)date('Y');
    $year = isset($data['year']) ? (int)$data['year'] : 0;
    if ($year < 1900 || $year > $current_year + 1) {
        $errors[] = "Year must be between 1900 and " . ($current_year + 1) . ".";
    }

    $price = isset($data['price']) ? (float)$data['price'] : 0;
    if ($price <= 0)            $errors[] = "Price must be positive.";
    if ($price > 100000000)     $errors[] = "Price exceeds the allowed limit.";

    $mileage = isset($data['mileage']) ? (int)$data['mileage'] : -1;
    if ($mileage < 0) $errors[] = "Mileage cannot be negative.";

    $allowed_fuel         = ['Petrol','Diesel','Hybrid','Electric'];
    $allowed_transmission = ['Manual','Automatic','CVT'];
    $allowed_condition    = ['New','Used'];

    if (!in_array($data['fuel_type']    ?? '', $allowed_fuel, true))         $errors[] = "Invalid fuel type.";
    if (!in_array($data['transmission'] ?? '', $allowed_transmission, true)) $errors[] = "Invalid transmission.";
    if (!in_array($data['condition']    ?? '', $allowed_condition, true))    $errors[] = "Invalid condition.";

    if (strlen($description) > 1000) $errors[] = "Description too long (max 1000).";

    return $errors;
}
