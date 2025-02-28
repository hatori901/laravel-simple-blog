<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-10 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            @auth
            {{-- for authenticated users --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="space-y-6 p-6">
                    <h2 class="text-lg font-semibold">Your Posts</h2>
                    @if($posts->isEmpty())
                    <p>You have no posts yet.<a href="{{ route('posts.create') }}" class="text-blue-500"> Create one</a>.</p>
                    @else
                    <a href="{{ route('posts.create') }}" class="text-blue-500">Create a new post</a>
                    @endif
                    @foreach($posts as $post)
                    <div class="rounded-md border p-5 shadow">
                        <div class="flex items-center gap-2">
                            @if($post->status == 'draft')
                            <span class="flex-none rounded bg-gray-100 px-2 py-1 text-gray-800">Draft</span>
                            @else
                                @if($post->published_at > now())
                                <span class="flex-none rounded bg-yellow-100 px-2 py-1 text-yellow-800">Scheduled</span>
                                @else
                                <span class="flex-none rounded bg-green-100 px-2 py-1 text-green-800">Active</span>
                                @endif
                            @endif
                            <h3>
                                <a href="{{ route('posts.show',$post->slug) }}" class="text-blue-500">
                                    {{ $post->title }}
                                </a>
                            </h3>
                        </div>
                        <div class="mt-4 flex items-end justify-between">
                            <div>
                                <div>Published: {{Carbon\Carbon::parse($post->published_at)->format('Y-m-d')}}</div>
                                <div>Updated: {{Carbon\Carbon::parse($post->updated_at)->format('Y-m-d')}}</div>
                            </div>
                            <div>
                                <a href="{{ route('posts.show',$post->slug) }}" class="text-blue-500">Detail</a> /
                                <a href="{{ route('posts.edit', $post) }}" class="text-blue-500">Edit</a> /
                                <form action="{{ route('posts.destroy',$post) }}" method="POST" class="inline">
                                    @csrf
                                    @method('delete')
                                    <button class="text-red-500">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    {{ $posts->links() }}
                </div>
            </div>
            @else
            {{-- for gueset users --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>Please <a href="{{ route('login') }}" class="text-blue-500">login</a> or
                    <a href="{{ route('register') }}" class="text-blue-500">register</a>.</p>
                </div>
            </div>
            @endauth
            
        </div>
    </div>
</x-app-layout>
