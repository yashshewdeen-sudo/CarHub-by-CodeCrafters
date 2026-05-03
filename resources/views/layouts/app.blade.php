<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarHub Laravel — @yield('title', 'Home')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background:#fdfdfd; font-family: 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: 700; color: #0056b3 !important; }
        .car-card img { height: 200px; object-fit: cover; }
        .card-price { color: #0056b3; font-weight: 700; }
        main { margin-top: 90px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('listings.index') }}"><i class="fas fa-car-side"></i> CarHub Laravel</a>
        <div class="ms-auto d-flex">
            <a href="{{ route('listings.index') }}" class="nav-link me-3">Listings</a>
            @auth
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.listings') }}" class="nav-link me-3">Admin Panel</a>
                @endif
                @if (in_array(auth()->user()->role, ['Seller','Admin']))
                    <a href="{{ route('listings.create') }}" class="nav-link me-3">Sell</a>
                @endif
                <a href="{{ route('profile.show') }}" class="nav-link me-3">My Profile</a>

                {{-- Inbox with unread badge --}}
                @php
                    $unreadCount = \App\Models\Message::where('receiver_id', auth()->id())
                        ->where('is_read', false)->count();
                @endphp
                <a href="{{ route('messages.inbox') }}" class="nav-link me-3">
                    Inbox
                    @if ($unreadCount > 0)
                        <span class="badge bg-danger">{{ $unreadCount }}</span>
                    @endif
                </a>

                <span class="nav-link me-3 text-muted">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">Register</a>
            @endauth
        </div>
    </div>
</nav>

<main class="container">
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>

<footer class="text-center py-4 mt-5 text-muted small">
    © 2025 CarHub Laravel — CodeCrafters
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>