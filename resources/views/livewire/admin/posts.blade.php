<div class="mx-auto max-w-4xl">
    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Moderación de la comunidad</h1>
    <p class="text-sm text-slate-500">Revisa y elimina publicaciones o comentarios que incumplan las normas.</p>

    <input type="search" wire:model.live.debounce.350ms="search" placeholder="Buscar publicaciones…" class="input mt-5">

    <div class="mt-4 space-y-3" wire:loading.class="opacity-60">
        @forelse ($posts as $post)
            <article class="card" wire:key="post-{{ $post->id }}" x-data="{ open: false }">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-xs text-slate-500">
                            <span class="badge">{{ $post->kind_label }}</span>
                            {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }} · ♥ {{ $post->likes_count }}
                        </p>
                        <a href="{{ route('community.show', $post) }}" target="_blank" class="mt-1 block font-bold hover:text-brand-600">{{ $post->title }} ↗</a>
                        <p class="mt-1 line-clamp-2 whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $post->body }}</p>
                    </div>
                    <button wire:click="deletePost({{ $post->id }})"
                            wire:confirm="¿Eliminar esta publicación y sus comentarios?"
                            class="shrink-0 text-sm font-semibold text-rose-600 hover:underline">Eliminar</button>
                </div>

                @if ($post->comments->isNotEmpty())
                    <button @click="open = !open" class="mt-3 text-xs font-semibold text-brand-600">
                        <span x-text="open ? 'Ocultar comentarios' : 'Ver {{ $post->comments->count() }} comentarios'"></span>
                    </button>
                    <div x-show="open" x-cloak class="mt-2 space-y-2 border-l-2 border-slate-200 pl-4 dark:border-slate-700">
                        @foreach ($post->comments as $c)
                            <div class="flex items-start justify-between gap-3 text-sm" wire:key="comment-{{ $c->id }}">
                                <p><span class="font-semibold">{{ $c->user->name }}:</span> {{ $c->body }}</p>
                                <button wire:click="deleteComment({{ $c->id }})" wire:confirm="¿Eliminar comentario?" class="shrink-0 text-xs text-rose-600 hover:underline">Eliminar</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div class="card py-12 text-center text-slate-500">No hay publicaciones.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
</div>
