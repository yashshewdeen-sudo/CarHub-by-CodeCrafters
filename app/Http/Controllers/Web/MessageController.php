<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Listing;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Show inbox
    public function inbox()
    {
        $messages = Message::with(['sender', 'listing'])
            ->where('receiver_id', auth()->id())
            ->latest()
            ->get()
            ->groupBy('listing_id');

        return view('messages.inbox', compact('messages'));
    }

    // Show conversation thread for a listing
    public function thread(Listing $listing)
    {
        $userId = auth()->id();

        $messages = Message::with(['sender', 'receiver'])
            ->where('listing_id', $listing->id)
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
            })
            ->oldest()
            ->get();

        // Mark messages as read
        Message::where('listing_id', $listing->id)
            ->where('receiver_id', $userId)
            ->update(['is_read' => true]);

        // Find the other user in the conversation
        $otherUser = null;
        foreach ($messages as $msg) {
            if ($msg->sender_id !== $userId) {
                $otherUser = $msg->sender;
                break;
            }
            if ($msg->receiver_id !== $userId) {
                $otherUser = $msg->receiver;
                break;
            }
        }

        // Fallback: load the listing seller if otherUser is still null
        if (!$otherUser) {
            $otherUser = $listing->seller; // make sure 'seller' relation exists on Listing
        }

        return view('messages.thread', compact('messages', 'listing', 'otherUser'));
    }

    // Send initial message to seller
    public function send(Request $request, Listing $listing)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        Message::create([
            'listing_id'  => $listing->id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $listing->seller_id,
            'body'        => $request->body,
        ]);

        return back()->with('status', 'Message sent to seller!');
    }

    // Reply in thread
    public function reply(Request $request, Listing $listing)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        // Find the other person in the thread
        $userId = auth()->id();
        $lastMessage = Message::where('listing_id', $listing->id)
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })->latest()->first();

        $receiverId = $lastMessage->sender_id === $userId
            ? $lastMessage->receiver_id
            : $lastMessage->sender_id;

        Message::create([
            'listing_id'  => $listing->id,
            'sender_id'   => $userId,
            'receiver_id' => $receiverId,
            'body'        => $request->body,
        ]);

        return back()->with('status', 'Reply sent!');
    }
}