<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
    <a href="{{ route('community.index') }}" wire:navigate class="text-sm text-slate-500 hover:underline">← Volver a la comunidad</a>

    <article class="card mt-4">
        <div class="flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-full bg-brand-100 font-bold text-brand-700">{{ Str::upper(Str::substr($post->user->name, 0, 1)) }}</span>
            <div class="flex-1">
                <p class="font-semibold">{{ $post->user->name }}</p>
                <p class="text-xs text-slate-500">{{ $post->user->role_label }}{{ $post->user->institution ? ' · '.$post->user->institution : '' }} · {{ $post->created_at->diffForHumans() }}</p>
            </div>
            <span class="badge">{{ $post->kind_label }}</span>
        </div>

        <h1 class="mt-4 text-2xl font-extrabold text-slate-900 dark:text-white">{{ $post->title }}</h1>
        <p class="mt-3 whitespace-pre-line leading-7 text-slate-700 dark:text-slate-300">{{ $post->body }}</p>

        <div class="mt-5">
            <button wire:click="toggleLike" class="btn-ghost {{ $liked ? 'text-rose-600' : '' }}">
                {{ $liked ? '♥' : '♡' }} Me sirvió · {{ $post->likes_count }}
            </button>
        </div>
    </article>

    <h2 class="mt-8 text-lg font-bold">{{ $comments->count() }} {{ Str::plural('comentario', $comments->count()) }}</h2>

    <div class="mt-4 space-y-3">
        @foreach ($comments as $c)
            <div class="card py-4" wire:key="c-{{ $c->id }}">
                <div class="flex items-center justify-between text-sm">
                    <p class="font-semibold">{{ $c->user->name }} <span class="font-normal text-slate-500">· {{ $c->created_at->diffForHumans() }}</span></p>
                    @if (auth()->id() === $c->user_id || auth()->id() === $post->user_id)
                        <button wire:click="deleteComment({{ $c->id }})" wire:confirm="¿Eliminar comentario?" class="text-xs text-slate-400 hover:text-rose-600">Eliminar</button>
                    @endif
                </div>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-700 dark:text-slate-300">{{ $c->body }}</p>
            </div>
        @endforeach
    </div>

    @auth
        <form wire:submit="addComment" class="mt-6 space-y-3">
            <textarea wire:model="body" rows="3" placeholder="Escribe tu respuesta…" class="input"></textarea>
            @error('body') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
            <button class="btn-primary" wire:loading.attr="disabled">Comentar</button>
        </form>
    @else
        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Inicia sesión</a> para comentar.
        </p>
    @endauth
</div>
