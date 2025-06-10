{{-- resources/views/profile/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>

                    {{-- Logic for friendship status and action button --}}
                    @if(Auth::user()->id === $user->id)
                        <p class="mt-4 text-gray-600">This is your own profile.</p>
                    @elseif(Auth::user()->isFriendWith($user))
                        <p class="mt-4 text-green-600">You are friends with {{ $user->name }}.</p>

                        <h3 class="font-semibold text-lg mt-6 mb-2">Mutual Friends</h3>
                        @if($mutualFriends->isEmpty())
                            <p>You have no mutual friends with {{ $user->name }}.</p>
                        @else
                            <ul>
                                @foreach($mutualFriends as $mutualFriend)
                                    <li class="mb-1">
                                        <a href="{{ route('profile.show', $mutualFriend) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $mutualFriend->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    @else
                        <p class="mt-4 text-gray-600">You are not friends with {{ $user->name }}.</p>
                        {{-- Add friend request button logic here if not already friends --}}
                        @php
                            $currentUser = Auth::user();
                            $sentRequest = $currentUser->sentFriendRequests->where('recipient_id', $user->id)->where('status', 'pending')->isNotEmpty();
                            $receivedRequest = $currentUser->receivedFriendRequests->where('sender_id', $user->id)->where('status', 'pending')->isNotEmpty();
                        @endphp

                        @if($sentRequest)
                            <p class="mt-4 text-gray-500">Friend request sent.</p>
                        @elseif($receivedRequest)
                            <p class="mt-4 text-gray-500">Pending friend request from {{ $user->name }}.</p>
                            <div class="flex space-x-2 mt-2">
                                {{-- Fetch the specific request object to pass to the route --}}
                                @php
                                    $pendingReq = $currentUser->receivedFriendRequests->where('sender_id', $user->id)->first();
                                @endphp
                                @if($pendingReq)
                                    <form action="{{ route('friend-requests.accept', $pendingReq) }}" method="POST">
                                        @csrf
                                        <x-primary-button>Accept Request</x-primary-button>
                                    </form>
                                    <form action="{{ route('friend-requests.decline', $pendingReq) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button>Decline Request</x-danger-button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('friend-requests.send', $user) }}" method="POST" class="mt-4">
                                @csrf
                                <x-primary-button>Send Friend Request</x-primary-button>
                            </form>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>