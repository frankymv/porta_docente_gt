<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6">
    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Comunidad</h1>
    <p class="mt-1 text-slate-500">Haz preguntas, comparte recursos y cuenta tus experiencias en el aula.</p>

    {{-- Nueva publicación --}}
    <div class="card mt-8" x-data="{ open: false }">
        @auth
            <button x-show="!open" @click="open = true" class="w-full rounded-xl border border-dashed border-slate-300 px-4 py-4 text-left text-slate-500 hover:border-brand-500 dark:border-slate-700">
                ¿Qué quieres compartir con la comunidad, {{ Str::before(auth()->user()->name, ' ') }}?
            </button>

            <form x-show="open" x-cloak wire:submit="publish" class="space-y-3">
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Models\Post::KINDS as $key => $label)
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="kind" value="{{ $key }}" class="peer sr-only">
                            <span class="inline-block rounded-full border border-slate-300 px-3 py-1 text-sm peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:text-white dark:border-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                <div>
                    <input type="text" wire:model="title" placeholder="Título" class="input" maxlength="150">
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <textarea wire:model="body" rows="4" placeholder="Cuéntanos más…" class="input"></textarea>
                    @error('body') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="open = false" class="btn-ghost">Cancelar</button>
                    <button class="btn-primary" wire:loading.attr="disabled" wire:target="publish">Publicar</button>
                </div>
            </form>
        @else
            <p class="text-center text-slate-600 dark:text-slate-400">
                <a href="{{ route('login') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Inicia sesión</a>
                o
                <a href="{{ route('register') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">crea una cuenta</a>
                para participar.
            </p>
        @endauth
    </div>

    {{-- Filtros --}}
    <div class="mt-6 flex flex-wrap gap-2">
        <button wire:click="$set('filter', '')" class="rounded-full px-4 py-1.5 text-sm font-medium {{ $filter === '' ? 'bg-brand-500 text-white' : 'bg-white text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">Todo</button>
        @foreach (\App\Models\Post::KINDS as $key => $label)
            <button wire:click="$set('filter', '{{ $key }}')" class="rounded-full px-4 py-1.5 text-sm font-medium {{ $filter === $key ? 'bg-brand-500 text-white' : 'bg-white text-slate-600 dark:bg-slate-900 dark:text-slate-300' }}">{{ $label }}s</button>
        @endforeach
    </div>

    {{-- Feed --}}
    <div class="mt-6 space-y-4">
        @forelse ($posts as $post)
            <article class="card" wire:key="post-{{ $post->id }}">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-brand-100 font-bold text-brand-700">{{ Str::upper(Str::substr($post->user->name, 0, 1)) }}</span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ $post->user->name }} <span class="font-normal text-slate-500">· {{ $post->user->role_label }}</span></p>
                        <p class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="badge">{{ $post->kind_label }}</span>
                </div>

                <a href="{{ route('community.show', $post) }}" wire:navigate class="mt-3 block text-lg font-bold text-slate-900 hover:text-brand-600 dark:text-white">{{ $post->title }}</a>
                <p class="mt-1 line-clamp-3 whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $post->body }}</p>

                <div class="mt-4 flex items-center gap-4 text-sm">
                    <button wire:click="toggleLike({{ $post->id }})" class="flex items-center gap-1 {{ in_array($post->id, $liked) ? 'font-semibold text-rose-600' : 'text-slate-500 hover:text-rose-600' }}">
                        {{ in_array($post->id, $liked) ? '♥' : '♡' }} {{ $post->likes_count }}
                    </button>
                    <a href="{{ route('community.show', $post) }}" wire:navigate class="text-slate-500 hover:text-brand-600">💬 {{ $post->comments_count }}</a>
                    @if (auth()->id() === $post->user_id)
                        <button wire:click="delete({{ $post->id }})" wire:confirm="¿Eliminar esta publicación?" class="ml-auto text-slate-400 hover:text-rose-600">Eliminar</button>
                    @endif
                </div>
            </article>
        @empty
            <div class="card py-12 text-center text-slate-500">Todavía no hay publicaciones. ¡Sé el primero!</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>
</div>
