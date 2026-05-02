<?php
// backend/controllers/create_listing_logic.php
require_once __DIR__ . '/../security/session_security.php';
require_once __DIR__ . '/../security/csrf.php';
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../validation/validator.php';
require_once __DIR__ . '/../validation/listing_validation.php';
require_once __DIR__ . '/../validation/upload_validation.php';

start_secure_session();
require_login();
require_role(['Seller', 'Admin'], '/carhub/frontend/pages/auth/login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /carhub/frontend/pages/listings/create_listing.php');
    exit();
}

if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['listing_errors'] = ["Invalid form submission."];
    header("Location: /carhub/frontend/pages/listings/create_listing.php");
    exit();
}

$errors = validate_listing($_POST);

$uploaded = [];
$target_dir = __DIR__ . '/../../uploads/cars/';

if (!empty($_FILES['images']['name'][0])) {
    $count = count($_FILES['images']['name']);
    if ($count > 5) $errors[] = "Maximum 5 images allowed.";

    for ($i = 0; $i < min($count, 5); $i++) {
        $f = [
            'name'     => $_FILES['images']['name'][$i],
            'type'     => $_FILES['images']['type'][$i],
            'tmp_name' => $_FILES['images']['tmp_name'][$i],
            'error'    => $_FILES['images']['error'][$i],
            'size'     => $_FILES['images']['size'][$i],
        ];
        $r = validate_and_upload($f, ['jpg','jpeg','png','webp'], 5, $target_dir);
        if ($r['success']) {
            $uploaded[] = ['file_path' => $r['file_path'], 'is_main' => ($i === 0) ? 1 : 0];
        } else {
            $errors[] = "Image " . ($i+1) . ": " . $r['message'];
        }
    }
} else {
    $errors[] = "At least one car image is required.";
}

if (!empty($errors)) {
    $_SESSION['listing_errors'] = $errors;
    header("Location: /carhub/frontend/pages/listings/create_listing.php");
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO car_listings
        (seller_id, make, model, year, mileage, price, fuel_type, transmission, condition_status, description, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,'Pending')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        clean_input($_POST['make']),
        clean_input($_POST['model']),
        (int)$_POST['year'],
        (int)$_POST['mileage'],
        (float)$_POST['price'],
        $_POST['fuel_type'],
        $_POST['transmission'],
        $_POST['condition'],
        clean_input($_POST['description'] ?? ''),
    ]);
    $listing_id = (int)$pdo->lastInsertId();

    $img_stmt = $pdo->prepare("INSERT INTO car_images (listing_id, file_path, is_main) VALUES (?,?,?)");
    foreach ($uploaded as $img) {
        $img_stmt->execute([$listing_id, $img['file_path'], $img['is_main']]);
    }

    $pdo->commit();
    $_SESSION['listing_success'] = "Listing created. Awaiting admin approval.";
} catch (Throwable $ex) {
    $pdo->rollBack();
    $_SESSION['listing_errors'] = ["Database error: " . $ex->getMessage()];
}

header("Location: /carhub/frontend/pages/listings/create_listing.php");
exit();
