@extends('layouts.app')
@section('title', 'Conversation')
@section('content')
    <h4 class="mb-1">
        <a href="{{ route('listings.show', $listing) }}">
            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
        </a>
    </h4>
    <p class="text-muted mb-4">Conversation with {{ $otherUser?->isAdmin() ? 'Admin' : ($otherUser?->name ?? 'Unknown') }}</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Messages --}}
    <div class="d-flex flex-column gap-3 mb-4" style="max-width: 700px;">
        @foreach ($messages as $msg)
            @php $isMine = $msg->sender_id === auth()->id(); @endphp
            <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="p-3 rounded-3 {{ $isMine ? 'bg-primary text-white' : 'bg-light text-dark' }}"
                     style="max-width: 75%;">
                    <div class="small mb-1 {{ $isMine ? 'text-white-50' : 'text-muted' }}">
                        {{ $isMine ? 'You' : $msg->sender->name }} •
                        {{ $msg->created_at->diffForHumans() }}
                    </div>
                    {{ $msg->body }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Reply form --}}
    <div style="max-width: 700px;">
        <form method="POST" action="{{ route('messages.reply', $listing) }}">
            @csrf
            <div class="mb-2">
                <textarea name="body" rows="3" class="form-control"
                    placeholder="Write a reply..." required></textarea>
            </div>
            <button class="btn btn-primary">Send Reply</button>
            <a href="{{ route('messages.inbox') }}" class="btn btn-outline-secondary ms-2">Back to Inbox</a>
        </form>
    </div>
@endsection