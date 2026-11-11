<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <a href="{{ route('space') }}" wire:navigate class="text-sm text-slate-500 hover:underline">← Mi Espacio</a>

    <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Mis contenidos</h1>
            <p class="text-sm text-slate-500">Artículos, documentos, webinars y asistencias técnicas que has publicado.</p>
        </div>
        <a href="{{ route('space.articles.create') }}" wire:navigate class="btn-primary">+ Nuevo contenido</a>
    </div>

    <div class="card mt-5 grid gap-3 sm:grid-cols-2">
        <input type="search" wire:model.live.debounce.350ms="search" placeholder="Buscar por título…" class="input">
        <select wire:model.live="status" class="input">
            <option value="">Todos los estados</option>
            <option value="published">Publicados</option>
            <option value="scheduled">Programados</option>
            <option value="draft">Borradores</option>
        </select>
    </div>

    <div class="mt-4 space-y-3" wire:loading.class="opacity-60">
        @forelse ($articles as $a)
            <div class="card flex flex-wrap items-center gap-4" wire:key="mine-{{ $a->id }}">
                @if ($a->cover_url)
                    <img src="{{ $a->cover_src }}" alt="" loading="lazy" class="h-16 w-24 shrink-0 rounded-xl object-cover">
                @else
                    <div class="grid h-16 w-24 shrink-0 place-items-center rounded-xl bg-slate-100 text-2xl text-slate-400 dark:bg-slate-800">🖼️</div>
                @endif

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="badge">{{ $a->type_label }}</span>
                        <span class="text-slate-500">{{ $a->category->name }}</span>
                        @if (! $a->published_at)
                            <span class="rounded-full bg-slate-200 px-2 py-0.5 font-semibold text-slate-700">Borrador</span>
                        @elseif ($a->published_at->isFuture())
                            <span class="rounded-full bg-amber-100 px-2 py-0.5 font-semibold text-amber-800">Programado · {{ $a->published_at->format('d/m/Y H:i') }}</span>
                        @else
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 font-semibold text-emerald-800">Publicado</span>
                        @endif
                    </div>
                    <a href="{{ route('space.articles.show', $a) }}" wire:navigate class="mt-1 block truncate font-bold hover:text-brand-600">{{ $a->title }}</a>
                    <p class="text-xs text-slate-500">Actualizado {{ $a->updated_at->diffForHumans() }} · {{ number_format($a->views) }} lecturas</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 text-sm">
                    <a href="{{ route('space.articles.show', $a) }}" wire:navigate class="font-semibold text-slate-600 hover:underline dark:text-slate-300">Ver</a>
                    <a href="{{ route('space.articles.edit', $a) }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Editar</a>
                    <button wire:click="togglePublish({{ $a->id }})" class="text-slate-500 hover:underline">
                        {{ $a->published_at ? 'A borrador' : 'Publicar' }}
                    </button>
                    <button wire:click="delete({{ $a->id }})" wire:confirm="¿Eliminar «{{ $a->title }}»? Esta acción no se puede deshacer." class="text-rose-600 hover:underline">Eliminar</button>
                </div>
            </div>
        @empty
            <div class="card py-14 text-center text-slate-500">
                Aún no has creado contenidos.
                <a href="{{ route('space.articles.create') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Crea el primero</a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $articles->links() }}</div>
</div>
