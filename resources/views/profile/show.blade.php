@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm p-4">
                <h3 class="mb-3">My Profile</h3>

                <table class="table table-borderless">
                    <tr><td>Name</td><td><strong>{{ $user->name }}</strong></td></tr>
                    <tr><td>Email</td><td><strong>{{ $user->email }}</strong></td></tr>
                    <tr><td>Phone</td><td><strong>{{ $user->phone ?? '—' }}</strong></td></tr>
                    <tr>
                        <td>Role</td>
                        <td>
                            @if ($user->role === 'Admin')
                                <span class="badge bg-danger">Admin</span>
                            @elseif ($user->role === 'Seller')
                                <span class="badge bg-success">Seller</span>
                            @else
                                <span class="badge bg-secondary">Buyer</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if ($user->role === 'Buyer')
                    <hr>
                    <h5>Upgrade to Seller</h5>
                    <p class="text-muted small">As a seller you can list cars for sale on CarHub.</p>
                    <form method="POST" action="{{ route('profile.upgrade') }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-primary w-100">
                            Upgrade to Seller Account
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection