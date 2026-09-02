{{--
    The parts of WordPress's toolbar that earn their place: a way back to the
    site, a New menu, and the pending comment count visible from every screen
    rather than only from the screen that happens to list them.
--}}
@php($pendingComments = \App\Models\Comment::where('status', 'pending')->count())

<div class="flex items-center gap-x-1">
    <a href="{{ url('/blog') }}" target="_blank" rel="noopener"
       class="wp-topbar-link" title="Open the blog in a new tab">
        <x-filament::icon icon="heroicon-m-home" class="h-4 w-4" />
        <span class="hidden sm:inline">Visit site</span>
    </a>

    <a href="{{ route('filament.admin.resources.comments.index') }}" class="wp-topbar-link">
        <x-filament::icon icon="heroicon-m-chat-bubble-left" class="h-4 w-4" />
        <span class="hidden sm:inline">Comments</span>
        @if($pendingComments > 0)
            <span class="wp-bubble">{{ $pendingComments }}</span>
        @endif
    </a>

    <x-filament::dropdown placement="bottom-start">
        <x-slot name="trigger">
            <button type="button" class="wp-topbar-link">
                <x-filament::icon icon="heroicon-m-plus" class="h-4 w-4" />
                <span class="hidden sm:inline">New</span>
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach([
                ['Post', 'filament.admin.resources.posts.create'],
                ['Category', 'filament.admin.resources.categories.create'],
                ['Media', 'filament.admin.resources.media.index'],
                ['Broadcast', 'filament.admin.resources.broadcasts.create'],
                ['User', 'filament.admin.resources.users.create'],
            ] as [$label, $route])
                @if(\Illuminate\Support\Facades\Route::has($route))
                    <x-filament::dropdown.list.item :href="route($route)" tag="a">
                        {{ $label }}
                    </x-filament::dropdown.list.item>
                @endif
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
