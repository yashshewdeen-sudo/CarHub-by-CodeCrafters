<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
require_once __DIR__ . '/../../../backend/security/csrf.php';
start_secure_session();
require_login();
require_role(['Seller','Admin']);
$csrf = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Sell Your Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
    <style>
        .img-thumb-wrap {
            position: relative; width: 140px; flex-shrink: 0; cursor: pointer;
        }
        .img-thumb-wrap img {
            width: 140px; height: 105px; object-fit: cover;
            border-radius: 6px; border: 3px solid #dee2e6; transition: border-color .2s;
        }
        .img-thumb-wrap.is-main img { border-color: #0056b3; }
        .main-badge {
            display: none; position: absolute; bottom: 6px; left: 6px;
            font-size: .7rem; padding: 2px 6px;
        }
        .img-thumb-wrap.is-main .main-badge { display: block; }
        .remove-btn {
            position: absolute; top: 4px; right: 4px;
            width: 22px; height: 22px; border-radius: 50%;
            font-size: .7rem; line-height: 22px; text-align: center; padding: 0;
        }
        #previewRow { gap: 12px; flex-wrap: wrap; min-height: 40px; }
        #dropZone {
            border: 2px dashed #adb5bd; border-radius: 10px;
            padding: 30px; text-align: center; cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        #dropZone.drag-over { border-color: #0056b3; background: #eaf1fb; }
    </style>
</head>
<body class="bg-light">
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content" style="max-width:900px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="fas fa-car"></i> List Your Car for Sale</h3>
        </div>
        <div class="card-body">
            <?php
            if (!empty($_SESSION['listing_errors'])) {
                echo '<div class="alert alert-danger"><ul>';
                foreach ($_SESSION['listing_errors'] as $err) echo '<li>' . htmlspecialchars($err) . '</li>';
                echo '</ul></div>';
                unset($_SESSION['listing_errors']);
            }
            if (!empty($_SESSION['listing_success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['listing_success']) . '</div>';
                unset($_SESSION['listing_success']);
            }
            ?>
            <form id="listingForm" action="/carhub/backend/controllers/create_listing_logic.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <h5 class="mb-3">Vehicle Details</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Make</label>
                        <input type="text" name="make" class="form-control" required minlength="2" maxlength="50"></div>
                    <div class="col-md-6"><label class="form-label">Model</label>
                        <input type="text" name="model" class="form-control" required minlength="1" maxlength="50"></div>
                    <div class="col-md-3"><label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" min="1900" max="<?= (int)date('Y')+1 ?>" required></div>
                    <div class="col-md-3"><label class="form-label">Price (Rs)</label>
                        <input type="number" name="price" class="form-control" min="1" step="0.01" required></div>
                    <div class="col-md-3"><label class="form-label">Mileage (km)</label>
                        <input type="number" name="mileage" class="form-control" min="0" required></div>
                    <div class="col-md-3"><label class="form-label">Condition</label>
                        <select name="condition" class="form-select" required>
                            <option value="Used">Used</option>
                            <option value="New">New</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select" required>
                            <?php foreach (['Petrol','Diesel','Hybrid','Electric'] as $f): ?>
                                <option value="<?= $f ?>"><?= $f ?></option>
                            <?php endforeach; ?>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Transmission</label>
                        <select name="transmission" class="form-select" required>
                            <?php foreach (['Automatic','Manual','CVT'] as $t): ?>
                                <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select></div>
                    <div class="col-12"><label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" maxlength="1000"></textarea></div>
                </div>

                <hr class="my-4">
                <h5 class="mb-1">Photos</h5>
                <p class="text-muted small mb-3">
                    Up to 5 images · JPG/PNG/WebP · max 5 MB each.<br>
                    <strong>Click any preview to set it as the cover image</strong> (shown in listings). Blue border = cover.
                </p>

                <div id="dropZone" class="mb-3">
                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2 d-block"></i>
                    <p class="mb-0 text-muted">Drag &amp; drop images here, or <strong>click to browse</strong></p>
                    <input type="file" id="imageInput" name="images[]" multiple
                           accept="image/jpeg,image/png,image/webp" style="display:none;" required>
                </div>

                <div id="previewRow" class="d-flex mb-2"></div>
                <div id="imageError" class="text-danger small mb-3" style="display:none;"></div>

                <button class="btn btn-primary w-100 btn-lg mt-2">
                    <i class="fas fa-paper-plane"></i> Submit Listing
                </button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const dropZone   = document.getElementById('dropZone');
    const imageInput = document.getElementById('imageInput');
    const previewRow = document.getElementById('previewRow');
    const imageError = document.getElementById('imageError');
    const MAX_FILES  = 5, MAX_BYTES = 5 * 1024 * 1024;
    const VALID      = ['image/jpeg','image/png','image/webp'];

    let files = []; // File objects in display order; index 0 = cover

    dropZone.addEventListener('click', () => imageInput.click());
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('drag-over'); addFiles([...e.dataTransfer.files]); });
    imageInput.addEventListener('change', () => { addFiles([...imageInput.files]); imageInput.value = ''; });

    function addFiles(incoming) {
        imageError.style.display = 'none';
        const errs = [];
        for (const f of incoming) {
            if (files.length >= MAX_FILES)      { errs.push('Maximum 5 images allowed.'); break; }
            if (!VALID.includes(f.type))        { errs.push(f.name + ': not a valid image type.'); continue; }
            if (f.size > MAX_BYTES)             { errs.push(f.name + ': exceeds 5 MB.'); continue; }
            files.push(f);
        }
        if (errs.length) { imageError.textContent = errs.join(' '); imageError.style.display = 'block'; }
        render(); sync();
    }

    function render() {
        previewRow.innerHTML = '';
        files.forEach((f, i) => {
            const wrap  = document.createElement('div');
            wrap.className = 'img-thumb-wrap' + (i === 0 ? ' is-main' : '');

            const img   = document.createElement('img');
            img.src     = URL.createObjectURL(f);
            img.alt     = f.name;
            img.title   = 'Click to set as cover';

            const badge = document.createElement('span');
            badge.className  = 'main-badge badge bg-primary';
            badge.textContent = 'Cover';

            const rm = document.createElement('button');
            rm.type  = 'button';
            rm.className = 'remove-btn btn btn-danger';
            rm.innerHTML = '&times;';
            rm.title = 'Remove image';

            wrap.append(img, badge, rm);
            previewRow.appendChild(wrap);

            // Set as cover: move to front
            img.addEventListener('click', () => {
                files.splice(i, 1);
                files.unshift(f);
                render(); sync();
            });

            // Remove
            rm.addEventListener('click', e => {
                e.stopPropagation();
                files.splice(i, 1);
                render(); sync();
            });
        });
    }

    function sync() {
        // Push current file order back into the <input> via DataTransfer
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        imageInput.files    = dt.files;
        imageInput.required = files.length === 0;
    }

    document.getElementById('listingForm').addEventListener('submit', e => {
        if (files.length === 0) {
            e.preventDefault();
            imageError.textContent = 'At least one image is required.';
            imageError.style.display = 'block';
        }
    });
})();
</script>
</body>
</html>
