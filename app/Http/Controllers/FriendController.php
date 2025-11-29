<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
       
        $friendsSent = $user->friends;

      
        $friendsReceived = $user->friendsReceived;

       
        $friends = $friendsSent->merge($friendsReceived);
        
      
        $pendingRequests = $user->friendRequests;
        
      
        $excludeIds = collect([$user->id]);
        $excludeIds = $excludeIds->merge($friends->pluck('id'));
        $excludeIds = $excludeIds->merge($pendingRequests->pluck('id'));
        
    
        $sentRequests = Friendship::where('user_id', $user->id)->pluck('friend_id');
        $excludeIds = $excludeIds->merge($sentRequests);

      
        $suggestedFriends = User::whereNotIn('id', $excludeIds->unique()->toArray())
            ->inRandomOrder() 
            ->take(5)
            ->get();

        return view('friends', compact('friends', 'pendingRequests', 'suggestedFriends'));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $user = Auth::user();

      
        $friendsSentIds = $user->friends->pluck('id');
        $friendsReceivedIds = $user->friendsReceived->pluck('id'); 
        
        $sentRequestsIds = Friendship::where('user_id', $user->id)->pluck('friend_id');
        $receivedRequestsIds = Friendship::where('friend_id', $user->id)->pluck('user_id');

       
        $allExcludedIds = collect([$user->id])
                            ->merge($friendsSentIds)
                            ->merge($friendsReceivedIds)
                            ->merge($sentRequestsIds)
                            ->merge($receivedRequestsIds)
                            ->unique()
                            ->toArray();

       
        $users = User::whereNotIn('id', $allExcludedIds)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->take(10)
            ->get();

        return response()->json($users);
    }

   
    public function sendRequest(User $user)
    {
        $currentUser = Auth::user();

        if ($currentUser->id === $user->id) {
            return back()->with('error', 'No puedes enviarte una solicitud a ti mismo.');
        }

    
        $existingFriendship = Friendship::where(function($q) use ($currentUser, $user) {
            $q->where('user_id', $currentUser->id)->where('friend_id', $user->id);
        })->orWhere(function($q) use ($currentUser, $user) {
            $q->where('user_id', $user->id)->where('friend_id', $currentUser->id);
        })->first();

        if ($existingFriendship) {
            return back()->with('error', 'Ya existe una solicitud o amistad con este usuario.');
        }

      
        Friendship::create([
            'user_id' => $currentUser->id,
            'friend_id' => $user->id,
            'accepted' => false
        ]);

        return back()->with('success', 'Solicitud enviada a ' . $user->name);
    }

  
    public function acceptRequest(User $user)
    {
       
        $friendship = Friendship::where('user_id', $user->id)
            ->where('friend_id', Auth::id())
            ->where('accepted', false)
            ->firstOrFail();

        $friendship->update(['accepted' => true]);

        return back()->with('success', '¡Ahora eres amigo de ' . $user->name . '!');
    }

   
    public function rejectRequest(User $user)
    {
        $friendship = Friendship::where('user_id', $user->id)
            ->where('friend_id', Auth::id())
            ->where('accepted', false)
            ->firstOrFail();

        $friendship->delete();

        return back()->with('success', 'Solicitud rechazada.');
    }

    
    public function removeFriend(User $user)
    {
        
        $friendship = Friendship::where(function($query) use ($user) {
            $query->where('user_id', Auth::id())->where('friend_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('user_id', $user->id)->where('friend_id', Auth::id());
        })->firstOrFail();

        $friendship->delete();

        return back()->with('success', 'Amistad eliminada.');
    }
}