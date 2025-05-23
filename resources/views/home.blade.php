<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-10 sm:px-6 lg:px-8">

            {{-- for gueset users --}}
            @if (!Auth::check())
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p>Please <a href="{{ route('login') }}" class="text-blue-500">login</a> or
                        <a href="{{ route('register') }}" class="text-blue-500">register</a>.</p>
                    </div>
                </div>
            @endif

            {{-- for authenticated users --}}
            @if (Auth::check())
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="space-y-6 p-6">
                        <h2 class="text-lg font-semibold">Your Posts</h2>

                        @forelse ($posts as $item)
                            <div class="rounded-md border p-5 shadow">
                                <div class="flex items-center gap-2">
                                    @if ($item->is_draft)
                                        <span class="flex-none rounded bg-gray-100 px-2 py-1 text-gray-800">Draft</span>
                                    @elseif (!$item->is_draft && $item->publish_date > now())
                                        <span class="flex-none rounded bg-yellow-100 px-2 py-1 text-yellow-800">Scheduled</span>
                                    @else
                                        <span class="flex-none rounded bg-green-100 px-2 py-1 text-green-800">Active</span>
                                    @endif
                                    <h3><a href="{{ route('posts.internal', ['post' => $item->id]) }}" class="text-blue-500">{{$item->title}}</a></h3>
                                </div>
                                <div class="mt-4 flex items-end justify-between">
                                    <div>
                                        <div>Published: {{$item->publish_date}}</div>
                                        <div>Updated: {{$item->updated_at->format('Y-m-d')}}</div>
                                    </div>
                                    <div>
                                        <a href="{{ route('posts.internal', ['post' => $item->id]) }}" class="text-blue-500">Detail</a> /
                                        <a href="{{ route('posts.edit', ['post' => $item->id]) }}" class="text-blue-500">Edit</a> /
                                        <form action="{{ route('posts.destroy', ['post' => $item->id]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse

                        <div>
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
