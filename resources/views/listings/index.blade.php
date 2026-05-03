@extends('layouts.app')
@section('title', 'Browse')
@section('content')

<h2 class="mb-4">Browse Listings</h2>

<form method="GET" class="mb-4">
    <div class="row g-2 align-items-end">

        {{-- Search --}}
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Search</label>
            <input type="text" name="search" id="searchInput" class="form-control" value="{{ request('search') }}" placeholder="Make or model...">
        </div>

        {{-- Make --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Make</label>
            <select name="make" class="form-select">
                <option value="">All Makes</option>
                @foreach ($makes as $make)
                    <option value="{{ $make }}" {{ request('make') == $make ? 'selected' : '' }}>{{ $make }}</option>
                @endforeach
            </select>
        </div>

        {{-- Year Range --}}
        <div class="col-md-1">
            <label class="form-label small text-muted mb-1">Year from</label>
            <select name="year_from" class="form-select">
                <option value="">Any</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ request('year_from') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label small text-muted mb-1">Year to</label>
            <select name="year_to" class="form-select">
                <option value="">Any</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ request('year_to') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>

        {{-- Price Range --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Price (Rs)</label>
            <div class="input-group">
                <input type="number" name="price_min" class="form-control" placeholder="Min" value="{{ request('price_min') }}">
                <input type="number" name="price_max" class="form-control" placeholder="Max" value="{{ request('price_max') }}">
            </div>
        </div>

        {{-- Fuel Type --}}
        <div class="col-md-1">
            <label class="form-label small text-muted mb-1">Fuel</label>
            <select name="fuel_type" class="form-select">
                <option value="">All</option>
                @foreach (['Petrol','Diesel','Electric','Hybrid'] as $fuel)
                    <option value="{{ $fuel }}" {{ request('fuel_type') == $fuel ? 'selected' : '' }}>{{ $fuel }}</option>
                @endforeach
            </select>
        </div>

        {{-- Transmission --}}
        <div class="col-md-1">
            <label class="form-label small text-muted mb-1">Gearbox</label>
            <select name="transmission" class="form-select">
                <option value="">All</option>
                @foreach (['Manual','Automatic'] as $trans)
                    <option value="{{ $trans }}" {{ request('transmission') == $trans ? 'selected' : '' }}>{{ $trans }}</option>
                @endforeach
            </select>
        </div>

        {{-- Mileage Range --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Mileage (km)</label>
            <div class="input-group">
                <input type="number" name="mileage_min" class="form-control" placeholder="Min" value="{{ request('mileage_min') }}">
                <input type="number" name="mileage_max" class="form-control" placeholder="Max" value="{{ request('mileage_max') }}">
            </div>
        </div>

        {{-- Status --}}
        <div class="col-md-2">
            <label class="form-label small text-muted mb-1">Status</label>
            <select name="status" class="form-select">
                <option value="both"   {{ request('status','both') == 'both'   ? 'selected' : '' }}>Active & Sold</option>
                <option value="Active" {{ request('status') == 'Active'         ? 'selected' : '' }}>Active only</option>
                <option value="Sold"   {{ request('status') == 'Sold'           ? 'selected' : '' }}>Sold only</option>
            </select>
        </div>

        {{-- Tags --}}
        <div class="col-12">
            <label class="form-label small text-muted mb-1">Tags</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input" type="checkbox" name="tags[]"
                            id="tag{{ $tag->id }}" value="{{ $tag->id }}"
                            {{ in_array($tag->id, (array) request('tags')) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="tag{{ $tag->id }}">{{ $tag->name }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Buttons --}}
        <div class="col-12 d-flex gap-2 mt-1">
            <button class="btn btn-primary px-4">Filter</button>
            <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>

    </div>
</form>

{{-- Active filter summary --}}
@php
    $activeFilters = array_filter(request()->only(['search','make','year_from','year_to','price_min','price_max','fuel_type','transmission','mileage_max','tags']));
@endphp
@if(count($activeFilters))
    <p class="text-muted small mb-3">
        Showing {{ $listings->total() }} result(s) — <a href="{{ route('listings.index') }}">clear all filters</a>
    </p>
@endif

{{-- Listings grid --}}
<div class="row g-4" id="listingsGrid">
    @forelse ($listings as $car)
        <div class="col-md-4">
            <div class="card car-card h-100 shadow-sm">
                @php $cover = $car->images->firstWhere('is_main', 1) ?? $car->images->first(); @endphp
                @if ($cover)
                    <img src="{{ asset('storage/' . $cover->path) }}" class="card-img-top" alt="" style="height:200px;object-fit:cover;">
                @else
                    <img src="/images/car{{ ($car->id % 3) + 1 }}.jpg" class="card-img-top" alt="" style="height:200px;object-fit:cover;">
                @endif

                {{-- Sold ribbon --}}
                @if($car->status === 'Sold')
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sold</span>
                @endif

                <div class="card-body">
                    <h5 class="card-title">{{ $car->year }} {{ $car->make }} {{ $car->model }}</h5>
                    <p class="card-price">Rs {{ number_format($car->price) }}</p>
                    <p class="text-muted small">
                        <i class="fas fa-tachometer-alt"></i> {{ number_format($car->mileage) }} km •
                        <i class="fas fa-gas-pump"></i> {{ $car->fuel_type }} •
                        <i class="fas fa-cogs"></i> {{ $car->transmission }}
                    </p>
                    <div class="mb-2">
                        @foreach ($car->tags as $tag)
                            <span class="badge bg-secondary">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('listings.show', $car) }}" class="btn btn-outline-primary w-100 mt-1">View Details</a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-muted py-5">No listings found.</div>
    @endforelse
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $listings->links() }}
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {

    let searchTimer;
    const searchUrl = "{{ route('listings.search') }}";

    $('#searchInput').on('input', function () {
        const query = $(this).val().trim();

        // Clear previous timer
        clearTimeout(searchTimer);

        // If empty, reload original results
        if (query.length === 0) {
            location.reload();
            return;
        }

        // Wait 400ms after user stops typing
        searchTimer = setTimeout(function () {
            $.ajax({
                url: searchUrl,
                method: 'GET',
                data: { search: query },
                beforeSend: function () {
                    $('#listingsGrid').html(`
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 text-muted">Searching...</p>
                        </div>
                    `);
                },
                success: function (listings) {
                    if (listings.length === 0) {
                        $('#listingsGrid').html(`
                            <div class="col-12 text-center text-muted py-5">
                                No listings found for "<strong>${query}</strong>"
                            </div>
                        `);
                        return;
                    }

                    let html = '';
                    $.each(listings, function (i, car) {
                        // Build tags
                        let tagsHtml = '';
                        $.each(car.tags, function (j, tag) {
                            tagsHtml += `<span class="badge bg-secondary">${tag}</span> `;
                        });

                        // Sold ribbon
                        let soldRibbon = car.status === 'Sold'
                            ? `<span class="badge bg-danger position-absolute top-0 end-0 m-2">Sold</span>`
                            : '';

                        html += `
                            <div class="col-md-4">
                                <div class="card car-card h-100 shadow-sm position-relative">
                                    ${soldRibbon}
                                    <img src="${car.image}" class="card-img-top"
                                         style="height:200px;object-fit:cover;" alt="">
                                    <div class="card-body">
                                        <h5 class="card-title">${car.title}</h5>
                                        <p class="card-price">${car.price}</p>
                                        <p class="text-muted small">
                                            <i class="fas fa-tachometer-alt"></i> ${car.mileage} •
                                            <i class="fas fa-gas-pump"></i> ${car.fuel_type} •
                                            <i class="fas fa-cogs"></i> ${car.transmission}
                                        </p>
                                        <div class="mb-2">${tagsHtml}</div>
                                        <a href="${car.url}" class="btn btn-outline-primary w-100 mt-1">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    $('#listingsGrid').html(html);
                },
                error: function () {
                    $('#listingsGrid').html(`
                        <div class="col-12 text-center text-danger py-5">
                            Something went wrong. Please try again.
                        </div>
                    `);
                }
            });
        }, 400);
    });

});
</script>
@endsection