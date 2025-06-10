
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Friend Requests') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($pendingRequests->isEmpty())
                        <p>You have no pending friend requests.</p>
                    @else
                        <ul>
                            @foreach($pendingRequests as $request)
                                <li class="mb-2 p-2 border rounded flex justify-between items-center">
                                    <a href="{{ route('profile.show', $request->sender) }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $request->sender->name }}
                                    </a>
                                    <div class="flex space-x-2">
                                        <form action="{{ route('friend-requests.accept', $request) }}" method="POST">
                                            @csrf
                                            <x-primary-button>Accept</x-primary-button>
                                        </form>
                                        <form action="{{ route('friend-requests.decline', $request) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>Decline</x-danger-button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>