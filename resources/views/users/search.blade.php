{{-- resources/views/users/search.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('users.search') }}" method="GET" class="mb-4">
                        <input type="text" name="query" placeholder="Search by name or email..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full" value="{{ request('query') }}">
                        <x-primary-button class="mt-4">Search</x-primary-button>
                    </form>

                    @if($users->isEmpty())
                        <p>No users found.</p>
                    @else
                        <ul>
                            @foreach($users as $user)
                               <li class="mb-2 p-2 border rounded flex justify-between items-center">
                                    <a href="{{ route('profile.show', $user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $user->name }} ({{ $user->email }})
                                    </a>
                                <div>
                                    @php
                                        $currentUser = Auth::user(); // Get current user in blade
                                        // This check is mainly for robustness, the controller should prevent it
                                        $isCurrentUser = ($currentUser->id === $user->id);

                                        $isFriend = $currentUser->isFriendWith($user);
                                        $sentRequest = $currentUser->sentFriendRequests->where('recipient_id', $user->id)->where('status', 'pending')->isNotEmpty();
                                        $receivedRequest = $currentUser->receivedFriendRequests->where('sender_id', $user->id)->where('status', 'pending')->isNotEmpty();
                                    @endphp

                                    @if($isCurrentUser)
                                        {{-- Do nothing, or display "Your Profile" --}}
                                        <span class="text-gray-500 px-3 py-1 rounded-full bg-blue-50 text-sm">Your Profile</span>
                                    @elseif($isFriend)
                                        <span class="text-gray-500 px-3 py-1 rounded-full bg-gray-100 text-sm">Friends</span>
                                    @elseif($sentRequest)
                                        <span class="text-gray-500 px-3 py-1 rounded-full bg-blue-100 text-sm">Request Sent</span>
                                    @elseif($receivedRequest)
                                        <p class="text-gray-500 text-sm">Pending Request</p>
                                        <div class="flex space-x-2 mt-1">
                                            {{-- Assuming you correctly fetch the pending request object here --}}
                                            @php
                                                $pendingReq = $currentUser->receivedFriendRequests->where('sender_id', $user->id)->first();
                                            @endphp
                                            @if($pendingReq)
                                                <form action="{{ route('friend-requests.accept', $pendingReq) }}" method="POST">
                                                    @csrf
                                                    <x-primary-button class="text-xs py-1">Accept</x-primary-button>
                                                </form>
                                                <form action="{{ route('friend-requests.decline', $pendingReq) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button class="text-xs py-1">Decline</x-danger-button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Only show add friend if none of the above conditions are met --}}
                                        <form action="{{ route('friend-requests.send', $user) }}" method="POST">
                                            @csrf
                                            <x-primary-button class="text-xs py-1">Add Friend</x-primary-button>
                                        </form>
                                    @endif
                                </div>
</li>
                            @endforeach
                        </ul>
                        {{ $users->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>