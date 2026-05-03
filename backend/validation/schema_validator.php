<?php
// backend/validation/schema_validator.php
// Requires: composer require opis/json-schema
// If composer not installed, falls back to a minimal hand-rolled validator.

function validate_json_against_schema($data, string $schema_file) : array {
    if (!file_exists($schema_file)) {
        return ['valid' => false, 'errors' => ["Schema not found: $schema_file"]];
    }

    $autoload = __DIR__ . '/../../vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
        if (class_exists(\Opis\JsonSchema\Validator::class)) {
            $validator = new \Opis\JsonSchema\Validator();
            $resolver  = $validator->resolver();
            $resolver->registerFile(
                'https://carhub.local/schemas/listing.schema.json',
                __DIR__ . '/../schemas/listing.schema.json'
            );
            $resolver->registerFile(
                'https://carhub.local/schemas/listings.schema.json',
                __DIR__ . '/../schemas/listings.schema.json'
            );
            $schema = json_decode(file_get_contents($schema_file));
            $result = $validator->validate(
                json_decode(json_encode($data)),
                $schema
            );
            if ($result->isValid()) return ['valid' => true, 'errors' => []];
            $err = $result->error();
            $msgs = [];
            $formatter = new \Opis\JsonSchema\Errors\ErrorFormatter();
            foreach ($formatter->format($err, false) as $path => $errs) {
                $msgs[] = "$path: " . implode('; ', $errs);
            }
            return ['valid' => false, 'errors' => $msgs];
        }
    }

    // Fallback: minimal validation (presence + types) so the project runs without composer.
    return validate_json_minimal($data, $schema_file);
}

function validate_json_minimal($data, string $schema_file) : array {
    $schema = json_decode(file_get_contents($schema_file), true);
    $errors = [];
    if (!is_array($data)) {
        return ['valid' => false, 'errors' => ['Data is not an object/array.']];
    }
    if (isset($schema['required'])) {
        foreach ($schema['required'] as $req) {
            if (!array_key_exists($req, (array)$data)) {
                $errors[] = "Missing required property: $req";
            }
        }
    }
    if (isset($schema['properties']['listings']) && isset($data['listings'])) {
        if (!is_array($data['listings'])) {
            $errors[] = "listings must be an array.";
        } else {
            $item_schema_file = __DIR__ . '/../schemas/listing.schema.json';
            $item_schema = json_decode(file_get_contents($item_schema_file), true);
            foreach ($data['listings'] as $i => $item) {
                foreach ($item_schema['required'] as $req) {
                    if (!array_key_exists($req, (array)$item)) {
                        $errors[] = "listings[$i]: missing $req";
                    }
                }
            }
        }
    }
    return ['valid' => empty($errors), 'errors' => $errors];
}
