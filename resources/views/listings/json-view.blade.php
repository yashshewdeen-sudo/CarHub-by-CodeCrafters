@extends('layouts.app')
@section('title', 'JSON Export' . $data['title'])
@section('content')

<div class="row">
    <div class="col-md-8">

        <h3 class="mb-1">JSON Export</h3>
        <p class="text-muted">
            File: <code>public/json/{{ $filename }}</code>
        </p>

        {{-- Validation status --}}
        @if (empty($validationErrors))
            <div class="alert alert-success">
                JSON is <strong>valid</strong> against the schema at consumption time.
            </div>
        @else
            <div class="alert alert-danger">
                JSON failed schema validation at consumption time:
                <ul class="mb-0 mt-2">
                    @foreach ($validationErrors as $err)
                        <li>{{ $err['property'] }}: {{ $err['message'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- PHP consumption: display data from JSON file --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                Data consumed from JSON file (PHP)
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td>Title</td><td><strong>{{ $data['title'] }}</strong></td></tr>
                    <tr><td>Price</td><td><strong>Rs {{ number_format($data['price']) }}</strong></td></tr>
                    <tr><td>Year</td><td><strong>{{ $data['year'] }}</strong></td></tr>
                    <tr><td>Make</td><td><strong>{{ $data['make'] }}</strong></td></tr>
                    <tr><td>Model</td><td><strong>{{ $data['model'] }}</strong></td></tr>
                    <tr><td>Mileage</td><td><strong>{{ number_format($data['mileage']) }} km</strong></td></tr>
                    <tr><td>Fuel</td><td><strong>{{ $data['fuel_type'] }}</strong></td></tr>
                    <tr><td>Transmission</td><td><strong>{{ $data['transmission'] }}</strong></td></tr>
                    <tr><td>Status</td><td><strong>{{ $data['status'] }}</strong></td></tr>
                    <tr><td>Seller</td><td><strong>{{ $data['seller']['name'] }}</strong></td></tr>
                    <tr><td>Tags</td><td><strong>{{ implode(', ', $data['tags']) }}</strong></td></tr>
                    <tr><td>Exported At</td><td><strong>{{ $data['exported_at'] }}</strong></td></tr>
                </table>
            </div>
        </div>

        {{-- Raw JSON --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                Raw JSON file contents
            </div>
            <div class="card-body p-0">
                <pre class="p-3 mb-0" style="background:#1e1e1e; color:#d4d4d4; border-radius: 0 0 8px 8px; overflow-x:auto;">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        {{-- jQuery/AJAX consumption --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                Data consumed via jQuery/AJAX
            </div>
            <div class="card-body">
                <button id="loadJsonBtn" class="btn btn-primary mb-3">
                    Load JSON via AJAX
                </button>
                <div id="ajaxResult"></div>
            </div>
        </div>

        <a href="{{ route('listings.show', $listing) }}" class="btn btn-outline-secondary">
            Back to Listing
        </a>

    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header">JSON Schema</div>
            <div class="card-body p-0">
                <pre class="p-3 mb-0" style="font-size:0.75rem; overflow-x:auto;">{{ json_encode(json_decode(file_get_contents(public_path('json/listing-schema.json'))), JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function () {

    $('#loadJsonBtn').on('click', function () {
        const jsonUrl = '/json/{{ $filename }}';

        $(this).prop('disabled', true).text('Loading...');

        // jQuery AJAX consuming the JSON file
        $.ajax({
            url: jsonUrl,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Build tags
                let tags = data.tags.length
                    ? data.tags.map(t => `<span class="badge bg-secondary">${t}</span>`).join(' ')
                    : '<em>None</em>';

                $('#ajaxResult').html(`
                    <div class="alert alert-success mb-3">
                        JSON successfully fetched and parsed via jQuery AJAX!
                    </div>
                    <table class="table table-bordered table-sm">
                        <tr><td>Title</td><td><strong>${data.title}</strong></td></tr>
                        <tr><td>Price</td><td><strong>Rs ${Number(data.price).toLocaleString()}</strong></td></tr>
                        <tr><td>Year</td><td><strong>${data.year}</strong></td></tr>
                        <tr><td>Make</td><td><strong>${data.make}</strong></td></tr>
                        <tr><td>Model</td><td><strong>${data.model}</strong></td></tr>
                        <tr><td>Fuel</td><td><strong>${data.fuel_type}</strong></td></tr>
                        <tr><td>Transmission</td><td><strong>${data.transmission}</strong></td></tr>
                        <tr><td>Status</td><td><strong>${data.status}</strong></td></tr>
                        <tr><td>Seller</td><td><strong>${data.seller.name}</strong></td></tr>
                        <tr><td>Tags</td><td>${tags}</td></tr>
                        <tr><td>Exported At</td><td>${data.exported_at}</td></tr>
                    </table>
                `);

                $('#loadJsonBtn').text('Loaded!').addClass('btn-success').removeClass('btn-primary');
            },
            error: function () {
                $('#ajaxResult').html(`
                    <div class="alert alert-danger">
                        Failed to load JSON file via AJAX.
                    </div>
                `);
                $('#loadJsonBtn').prop('disabled', false).text('Try Again');
            }
        });
    });

});
</script>

@endsection