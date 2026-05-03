@extends('layouts.app')
@section('title', $listing->make.' '.$listing->model)
@section('content')
    <div class="row">
        <div class="col-md-8">
            <h2>{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h2>
            <p class="text-muted">Listed by {{ $listing->seller->name }}</p>

            {{-- Status Badge --}}
            <span class="badge fs-6
                @if($listing->status === 'Active') bg-success
                @elseif($listing->status === 'Pending') bg-warning text-dark
                @elseif($listing->status === 'Sold') bg-secondary
                @else bg-danger
                @endif">
                {{ $listing->status }}
            </span>

            <hr>
            <p>{{ $listing->description }}</p>

            @foreach ($listing->tags as $tag)
                <span class="badge bg-info text-dark">{{ $tag->name }}</span>
            @endforeach

            @if ($listing->images->count() > 0)
                @php
                    $cover = $listing->images->firstWhere('is_main', 1) ?? $listing->images->first();
                    $thumbs = $listing->images->where('id', '!=', $cover->id);
                @endphp

                {{-- Main cover image --}}
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $cover->path) }}"
                        class="img-fluid rounded w-100" style="max-height: 400px; object-fit: cover;">
                </div>

                {{-- Thumbnails (excluding cover) --}}
                @if ($thumbs->count() > 0)
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @foreach ($thumbs as $img)
                            <img src="{{ asset('storage/' . $img->path) }}"
                                class="rounded" style="height: 100px; width: 150px; object-fit: cover;">
                        @endforeach
                    </div>
                @endif
            @endif

            {{-- Error message --}}
            @if (session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif

            {{-- Contact Seller Button --}}
            @guest
                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Contact Seller
                </button>

                {{-- Login/Register Modal --}}
                <div class="modal fade" id="loginModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content p-4 text-center">
                            <h5 class="mb-3">Sign in to contact the seller</h5>
                            <p class="text-muted">You need an account to send messages.</p>
                            <div class="d-flex gap-3 justify-content-center mt-2">
                                <a href="{{ route('login') }}" class="btn btn-primary px-4">Login</a>
                                <a href="{{ route('register') }}" class="btn btn-outline-primary px-4">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endguest

            @auth
                @if (auth()->id() !== $listing->seller_id && !auth()->user()->isAdmin())
                    {{-- Contact form for buyers --}}
                    <div class="mt-3" style="max-width: 500px;">
                        <h5>Contact Seller</h5>

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('messages.send', $listing) }}">
                            @csrf
                            <div class="mb-2">
                                <textarea name="body" rows="3" class="form-control"
                                    placeholder="Write your message to the seller..." required></textarea>
                            </div>
                            <button class="btn btn-primary">Send Message</button>
                            <a href="{{ route('messages.thread', $listing) }}" class="btn btn-outline-secondary ms-2">
                                View Conversation
                            </a>
                        </form>
                    </div>
                @endif
            @endauth

            @auth
                @if (auth()->id() === $listing->seller_id || auth()->user()->isAdmin())
                    <div class="mt-3 d-flex gap-2 flex-wrap">

                        {{-- Export JSON button --}}
                        <a href="{{ route('listings.json.export', $listing) }}"
                           class="btn btn-info btn-sm">
                            Export JSON
                        </a>

                        {{-- Mark as Sold (seller only, Active listings only) --}}
                        @if (auth()->id() === $listing->seller_id && $listing->status === 'Active')
                            <form method="POST" action="{{ route('listings.sold', $listing) }}"
                                  onsubmit="return confirm('Mark this listing as sold?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-warning btn-sm">Mark as Sold</button>
                            </form>
                        @endif

                        {{-- Delete button --}}
                        <form method="POST" action="{{ route('listings.destroy', $listing) }}"
                              onsubmit="return confirm('Delete this listing?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete Listing</button>
                        </form>

                    </div>
                @endif
            @endauth
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary fw-bold">Rs {{ number_format($listing->price) }}</h2>
                    <table class="table table-borderless mt-3">
                        <tr><td>Year</td><td><strong>{{ $listing->year }}</strong></td></tr>
                        <tr><td>Mileage</td><td><strong>{{ number_format($listing->mileage) }} km</strong></td></tr>
                        <tr><td>Fuel</td><td><strong>{{ $listing->fuel_type }}</strong></td></tr>
                        <tr><td>Transmission</td><td><strong>{{ $listing->transmission }}</strong></td></tr>
                        <tr><td>Condition</td><td><strong>{{ $listing->condition_status }}</strong></td></tr>
                        <tr><td>Category</td><td><strong>{{ $listing->category?->name ?? '—' }}</strong></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection