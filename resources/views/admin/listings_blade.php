@extends('layouts.app')
@section('title', 'Admin — Manage Listings')
@section('content')
    <h2 class="mb-4">Manage Listings</h2>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Seller</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($listings as $listing)
                <tr>
                    <td>{{ $listing->id }}</td>
                    <td>
                        <a href="{{ route('listings.show', $listing) }}">
                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                        </a>
                    </td>
                    <td>{{ $listing->seller->name }}</td>
                    <td>Rs {{ number_format($listing->price) }}</td>
                    <td>
                        @if ($listing->status === 'Active')
                            <span class="badge bg-success">Active</span>
                        @elseif ($listing->status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td class="d-flex gap-2">
                        @if ($listing->status !== 'Active')
                            <form method="POST" action="{{ route('admin.approve', $listing) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success">Approve</button>
                            </form>
                        @endif
                        @if ($listing->status !== 'Rejected')
                            <form method="POST" action="{{ route('admin.reject', $listing) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-danger">Reject</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">No listings found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $listings->links() }}
@endsection