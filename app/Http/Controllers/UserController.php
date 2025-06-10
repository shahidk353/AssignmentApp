<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function search(Request $request)
    {
        $query = $request->input('query');
        $currentUser = Auth::user(); 
        $users = User::where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%{$query}%")
                             ->orWhere('email', 'like', "%{$query}%");
             })
             ->where('id', '!=', $currentUser->id)
             ->paginate(10);

        return view('users.search', compact('users', 'query'));
    }

  

    public function show(User $user)
    {
        $currentUser = Auth::user();
        $mutualFriends = collect();

      
        if (!$currentUser) {
            dd('ERROR: No user logged in. Cannot calculate mutual friends.');
        }

        if ($currentUser->id === $user->id) {
            return view('profile.show', compact('user', 'mutualFriends'));
        }

        $areTheyFriends = $currentUser->isFriendWith($user);
       
        if ($areTheyFriends) {
            $currentUserFriends = $currentUser->friends; 
            $viewedUserFriends = $user->friends;       

            $currentUserFriendsIds = $currentUserFriends->pluck('id');
            $viewedUserFriendsIds = $viewedUserFriends->pluck('id');

           
            $mutualFriendIds = $currentUserFriendsIds->intersect($viewedUserFriendsIds);
            $mutualFriends = User::whereIn('id', $mutualFriendIds)->get();
        }

        return view('profile.show', compact('user', 'mutualFriends'));
    }

}
