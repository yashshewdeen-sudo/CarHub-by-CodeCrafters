@extends('layouts.app')
@section('title', 'Inbox')
@section('content')
    <h2 class="mb-4">Inbox</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($messages->isEmpty())
        <p class="text-muted">No messages yet.</p>
    @else
        <div class="list-group">
            @foreach ($messages as $listingId => $thread)
                @php
                    $first = $thread->first();
                    $unread = $thread->where('is_read', false)->where('receiver_id', auth()->id())->count();
                @endphp
                <a href="{{ route('messages.thread', $first->listing) }}"
                   class="list-group-item list-group-item-action {{ $unread > 0 ? 'fw-bold' : '' }}">
                    <div class="d-flex justify-content-between">
                        <span>
                            {{ $first->listing->year }} {{ $first->listing->make }} {{ $first->listing->model }}
                        </span>
                        @if ($unread > 0)
                            <span class="badge bg-danger">{{ $unread }} new</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        From: {{ $first->sender->name }} •
                        {{ $first->created_at->diffForHumans() }}
                    </small>
                </a>
            @endforeach
        </div>
    @endif
@endsection