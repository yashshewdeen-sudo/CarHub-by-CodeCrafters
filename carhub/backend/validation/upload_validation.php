<?php
// backend/validation/upload_validation.php

function validate_and_upload(array $file, array $allowed_extensions, int $max_size_mb, string $target_dir) : array {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE  => 'File exceeds server size limit.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit.',
            UPLOAD_ERR_PARTIAL   => 'File only partially uploaded.',
            UPLOAD_ERR_NO_FILE   => 'No file uploaded.',
        ];
        return ['success' => false, 'message' => $upload_errors[$file['error'] ?? -1] ?? 'Unknown upload error.'];
    }

    $max_bytes = $max_size_mb * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        return ['success' => false, 'message' => "File too large. Max {$max_size_mb}MB."];
    }

    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_extensions, true)) {
        return ['success' => false, 'message' => "Invalid extension. Allowed: " . implode(', ', $allowed_extensions)];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $valid_mimes = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png', 'webp' => 'image/webp',
        'pdf' => 'application/pdf',
    ];
    if (!isset($valid_mimes[$file_ext]) || $valid_mimes[$file_ext] !== $mime) {
        return ['success' => false, 'message' => "File MIME does not match extension."];
    }

    if (!is_dir($target_dir) && !mkdir($target_dir, 0755, true)) {
        return ['success' => false, 'message' => "Cannot create upload directory."];
    }

    $new_name    = bin2hex(random_bytes(16)) . '.' . $file_ext;
    $destination = rtrim($target_dir, '/') . '/' . $new_name;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'message' => "Failed to move uploaded file."];
    }

    // Return a web-relative path for DB storage
    $web_path = 'uploads/cars/' . $new_name;

    return ['success' => true, 'file_path' => $web_path, 'file_name' => $new_name];
}
