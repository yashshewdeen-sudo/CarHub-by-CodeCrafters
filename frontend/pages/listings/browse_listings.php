<?php
require_once __DIR__ . '/../../../backend/security/session_security.php';
start_secure_session();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Browse Cars</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container main-content">
    <h2 class="mb-4">Browse Car Listings</h2>

    <div class="row mb-4 g-2">
        <div class="col-md-3">
            <input type="text" id="searchInput" class="form-control"
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                   placeholder="Search make or model...">
        </div>
        <div class="col-md-2">
            <input type="number" id="minPrice" class="form-control" placeholder="Min Price" min="0">
        </div>
        <div class="col-md-2">
            <input type="number" id="maxPrice" class="form-control"
                   value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"
                   placeholder="Max Price" min="0">
        </div>
        <div class="col-md-2">
            <select id="fuelFilter" class="form-select">
                <option value="">All Fuel Types</option>
                <?php foreach (['Petrol','Diesel','Hybrid','Electric'] as $f): ?>
                    <option value="<?= $f ?>" <?= ($_GET['fuel'] ?? '') === $f ? 'selected' : '' ?>><?= $f ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select id="statusFilter" class="form-select">
                <option value="both">Active &amp; Sold</option>
                <option value="Active">Active only</option>
                <option value="Sold">Sold only</option>
            </select>
        </div>
        <div class="col-md-1">
            <button id="filterBtn" class="btn btn-primary w-100"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <div class="row g-4" id="listingsContainer"></div>
    <nav aria-label="Listings pagination" class="my-4">
        <ul class="pagination justify-content-center" id="pagination"></ul>
    </nav>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const API = '/carhub/backend/api/get_listings.php';
let currentPage = 1;

function loadListings(page = 1) {
    currentPage = page;
    const statusVal = $('#statusFilter').val();
    const params = {
        search:    $('#searchInput').val() || '',
        min_price: $('#minPrice').val() || 0,
        max_price: $('#maxPrice').val() || 99999999,
        fuel:      $('#fuelFilter').val() || '',
        page:      page,
        per_page:  9
    };
    if (statusVal === 'both') {
        params.public = '1';          // Active + Sold
    } else if (statusVal === 'Active' || statusVal === 'Sold') {
        params.status = statusVal;    // single status
    } else {
        params.public = '1';          // fallback
    }
    $.getJSON(API, params, function (data) {
        const cars = data.listings || [];
        if (!cars.length) {
            $('#listingsContainer').html('<div class="col-12 text-center py-5"><p class="lead">No cars match your filters.</p></div>');
            $('#pagination').empty();
            return;
        }
        const html = cars.map(car => {
            const sold = car.status === 'Sold';
            const badge = sold ? '<span class="badge bg-danger position-absolute top-0 end-0 m-2" style="font-size:.85rem;">SOLD</span>' : '';
            const btn = sold
                ? `<a href="/carhub/frontend/pages/listings/view_listing.php?id=${car.listing_id}" class="btn btn-secondary w-100"><i class="fas fa-eye me-1"></i> View Sold Listing</a>`
                : `<a href="/carhub/frontend/pages/listings/view_listing.php?id=${car.listing_id}" class="btn btn-outline-primary w-100">View Details</a>`;
            return `
            <div class="col-md-4">
                <div class="card car-card h-100 ${sold ? 'opacity-75' : ''}">
                    <div class="position-relative">
                        <img src="/carhub/${car.main_image || 'images/car1.jpg'}" class="card-img-top" alt="${car.make} ${car.model}">
                        ${badge}
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${car.year} ${car.make} ${car.model}</h5>
                        <p class="card-price">Rs ${parseFloat(car.price).toLocaleString()}</p>
                        <p class="text-muted small">
                            <i class="fas fa-tachometer-alt"></i> ${parseInt(car.mileage).toLocaleString()} km •
                            <i class="fas fa-gas-pump"></i> ${car.fuel_type} •
                            <i class="fas fa-cogs"></i> ${car.transmission}
                        </p>
                        ${btn}
                    </div>
                </div>
            </div>`;
        }).join('');
        $('#listingsContainer').html(html);

        // Pagination
        let pg = '';
        for (let p = 1; p <= data.total_pages; p++) {
            pg += `<li class="page-item ${p === data.page ? 'active' : ''}">
                       <a class="page-link" href="#" data-page="${p}">${p}</a>
                   </li>`;
        }
        $('#pagination').html(pg);
    });
}

$(function () {
    loadListings();
    $('#filterBtn').on('click', () => loadListings(1));
    $('#searchInput, #minPrice, #maxPrice, #fuelFilter, #statusFilter').on('change', () => loadListings(1));
    $('#pagination').on('click', 'a', function (e) {
        e.preventDefault();
        loadListings(parseInt($(this).data('page'), 10));
    });
});
</script>
</body>
</html>
