<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FriendController extends Controller
{
    public function send(User $recipient)
    {
        $sender = auth()->user();

        if ($sender->id === $recipient->id) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }

        // Check if a request already exists in either direction
        if (FriendRequest::where(function ($query) use ($sender, $recipient) {
            $query->where('sender_id', $sender->id)
                  ->where('recipient_id', $recipient->id);
        })->orWhere(function ($query) use ($sender, $recipient) {
            $query->where('sender_id', $recipient->id)
                  ->where('recipient_id', $sender->id);
        })->exists()) {
            return back()->with('error', 'A friend request with this user already exists or is pending.');
        }

        // Check if they are already friends
        if ($sender->isFriendWith($recipient)) {
            return back()->with('error', 'You are already friends with this user.');
        }

        FriendRequest::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Friend request sent!');
    }


    public function pending()
    {
        $pendingRequests = auth()->user()->receivedFriendRequests()->where('status', 'pending')->get();
        return view('friend_requests.pending', compact('pendingRequests'));
    }

    public function accept(FriendRequest $friendRequest)
    {
        if ($friendRequest->recipient_id !== auth()->id()) {
            abort(403); // Not authorized to accept this request
        }

        if ($friendRequest->status !== 'pending') {
            return back()->with('error', 'This friend request is no longer pending.');
        }

        DB::transaction(function () use ($friendRequest) {
            $friendRequest->update(['status' => 'accepted']);

            // Add to the 'friends' table
            $friendRequest->recipient->friendsOfMine()->attach($friendRequest->sender_id);
        });

        return back()->with('success', 'Friend request accepted!');
    }

    public function decline(FriendRequest $friendRequest)
    {
        if ($friendRequest->recipient_id !== auth()->id()) {
            abort(403);
        }

        if ($friendRequest->status !== 'pending') {
            return back()->with('error', 'This friend request is no longer pending.');
        }

        $friendRequest->update(['status' => 'declined']);

        return back()->with('success', 'Friend request declined.');
    }
}