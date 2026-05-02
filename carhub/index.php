<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub - Find Your Perfect Car Match</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/carhub/css/style.css">
    <style>
        .dot { width:10px; height:10px; border-radius:50%; background:#ccc; cursor:pointer; display:inline-block; transition:background .3s; }
        .dot-active { background:#0056b3; }
        #featuredContainer { min-height: 320px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/frontend/includes/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold">Find Your Perfect Car Match</h1>
        <p class="lead">Buy and sell cars with confidence on CarHub.</p>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <form action="/carhub/frontend/pages/listings/browse_listings.php" method="GET" class="search-box">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" placeholder="Search make or model...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="fuel">
                                <option value="">Any Fuel Type</option>
                                <option value="Petrol">Petrol</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Electric">Electric</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="max_price">
                                <option value="">Max Price</option>
                                <option value="500000">Rs 500,000</option>
                                <option value="1000000">Rs 1,000,000</option>
                                <option value="5000000">Rs 5,000,000</option>
                                <option value="30000000">Rs 30,000,000</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Available Cars</h2>
            <small class="text-muted">Sold listings are not shown.</small>
        </div>
        <div id="rotationDots" class="d-flex gap-2 align-items-center"></div>
    </div>
    <div class="row g-4" id="featuredContainer">
        <div class="col-12 text-center text-muted py-5">
            <div class="spinner-border text-primary" role="status"></div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/frontend/includes/footer.php'; ?>

<style>
.dot { width:10px; height:10px; border-radius:50%; background:#ccc; cursor:pointer; transition:background .3s; display:inline-block; }
.dot-active { background:#0056b3; }
</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function () {
    const PAGE_SIZE = 3, ROTATE_MS = 5000;
    let allCars = [], currentIndex = 0, rotateTimer = null;

    function buildCard(car) {
        const img   = car.main_image ? '/carhub/' + car.main_image : '/carhub/images/car1.jpg';
        const price = parseFloat(car.price).toLocaleString();
        const miles = parseInt(car.mileage).toLocaleString();
        return `
            <div class="col-md-4">
                <div class="card car-card h-100">
                    <img src="${img}" class="card-img-top" alt="${car.make} ${car.model}">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${car.year} ${car.make} ${car.model}</h5>
                        <p class="card-price mb-2">Rs ${price}</p>
                        <div class="d-flex justify-content-between mb-3 text-muted small">
                            <span><i class="fas fa-tachometer-alt"></i> ${miles} km</span>
                            <span><i class="fas fa-gas-pump"></i> ${car.fuel_type}</span>
                            <span><i class="fas fa-cogs"></i> ${car.transmission}</span>
                        </div>
                        <a href="/carhub/frontend/pages/listings/view_listing.php?id=${car.listing_id}"
                           class="btn btn-outline-primary w-100 mt-auto">View Details</a>
                    </div>
                </div>
            </div>`;
    }

    function renderDots(slideIndex) {
        const total = Math.ceil(allCars.length / PAGE_SIZE);
        $('#rotationDots').html(
            Array.from({ length: total }, (_, i) =>
                `<span class="dot ${i === slideIndex ? 'dot-active' : ''}" data-slide="${i}"></span>`
            ).join('')
        );
    }

    function showSlide(slideIndex) {
        const total = Math.ceil(allCars.length / PAGE_SIZE);
        slideIndex  = ((slideIndex % total) + total) % total;
        currentIndex = slideIndex * PAGE_SIZE;
        let slice = allCars.slice(currentIndex, currentIndex + PAGE_SIZE);
        if (slice.length < PAGE_SIZE) {
            slice = slice.concat(allCars.slice(0, PAGE_SIZE - slice.length));
        }
        $('#featuredContainer').fadeOut(200, function () {
            $(this).html(slice.map(buildCard).join('')).fadeIn(300);
        });
        renderDots(slideIndex);
    }

    function startRotation() {
        clearInterval(rotateTimer);
        if (allCars.length > PAGE_SIZE) {
            rotateTimer = setInterval(function () {
                showSlide(Math.floor(currentIndex / PAGE_SIZE) + 1);
            }, ROTATE_MS);
        }
    }

    // Explicitly request only Active — sold/pending/rejected never shown on home
    $.getJSON('/carhub/backend/api/get_listings.php', { status: 'Active', per_page: 50 }, function (data) {
        allCars = data.listings || [];
        if (!allCars.length) {
            $('#featuredContainer').html('<div class="col-12 text-center text-muted py-5"><i class="fas fa-car fa-2x d-block mb-3"></i>No available listings right now.</div>');
            return;
        }
        showSlide(0);
        startRotation();
    }).fail(function () {
        $('#featuredContainer').html('<div class="col-12 text-center text-danger py-5">Failed to load listings.</div>');
    });

    $(document).on('click', '.dot', function () {
        showSlide(parseInt($(this).data('slide'), 10));
        startRotation(); // reset timer on manual nav
    });
});
</script>
</body>
</html>
