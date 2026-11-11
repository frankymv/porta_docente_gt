<div>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Contenidos</h1>
            <p class="text-sm text-slate-500">Artículos, documentos, webinars y podcast.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" wire:navigate class="btn-primary">+ Nuevo contenido</a>
    </div>

    {{-- Filtros --}}
    <div class="card mt-5 grid gap-3 sm:grid-cols-3">
        <input type="search" wire:model.live.debounce.350ms="search" placeholder="Buscar por título o autor…" class="input">
        <select wire:model.live="type" class="input">
            <option value="">Todos los tipos</option>
            @foreach (\App\Models\Article::TYPES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="status" class="input">
            <option value="">Todos los estados</option>
            <option value="published">Publicados</option>
            <option value="scheduled">Programados</option>
            <option value="draft">Borradores</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="card mt-4 overflow-x-auto p-0" wire:loading.class="opacity-60">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/50">
                <tr>
                    <th class="px-4 py-3">Título</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Lecturas</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($articles as $a)
                    <tr wire:key="row-{{ $a->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <td class="max-w-xs px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($a->cover_url)
                                    <img src="{{ $a->cover_src }}" alt="" loading="lazy" class="h-10 w-14 shrink-0 rounded-lg object-cover">
                                @else
                                    <div class="grid h-10 w-14 shrink-0 place-items-center rounded-lg bg-slate-100 text-slate-400 dark:bg-slate-800">🖼️</div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.articles.show', $a) }}" wire:navigate class="block truncate font-semibold text-slate-900 hover:text-brand-600 dark:text-white">{{ $a->title }}</a>
                                    <p class="text-xs text-slate-500">{{ $a->author_name ?? '—' }} · {{ $a->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $a->type_label }}</td>
                        <td class="px-4 py-3">{{ $a->category->name }}</td>
                        <td class="px-4 py-3">
                            @if (! $a->published_at)
                                <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">Borrador</span>
                            @elseif ($a->published_at->isFuture())
                                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800" title="{{ $a->published_at->format('d/m/Y H:i') }}">Programado</span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Publicado</span>
                            @endif
                            @if ($a->is_featured) <span title="Destacado">⭐</span> @endif
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ number_format($a->views) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right" x-data="{ menu: false }">
                            <a href="{{ route('admin.articles.show', $a) }}" wire:navigate class="font-semibold text-slate-600 hover:underline dark:text-slate-300">Ver</a>
                            <a href="{{ route('admin.articles.edit', $a) }}" wire:navigate class="ml-2 font-semibold text-brand-600 hover:underline">Editar</a>
                            <span class="relative">
                                <button @click="menu = !menu" @click.outside="menu = false" class="ml-2 rounded px-2 py-1 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">⋯</button>
                                <div x-show="menu" x-cloak x-transition class="absolute right-0 z-10 mt-1 w-44 rounded-xl bg-white py-1 text-left shadow-lg ring-1 ring-black/5 dark:bg-slate-800">
                                    @if ($a->published_at && $a->published_at->isPast())
                                        <a href="{{ route('articles.show', $a) }}" target="_blank" class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">Ver en el sitio ↗</a>
                                    @endif
                                    <button wire:click="togglePublish({{ $a->id }})" @click="menu = false" class="block w-full px-4 py-2 text-left hover:bg-slate-100 dark:hover:bg-slate-700">
                                        {{ $a->published_at ? 'Pasar a borrador' : 'Publicar ahora' }}
                                    </button>
                                    <button wire:click="toggleFeatured({{ $a->id }})" @click="menu = false" class="block w-full px-4 py-2 text-left hover:bg-slate-100 dark:hover:bg-slate-700">
                                        {{ $a->is_featured ? 'Quitar destacado' : 'Destacar' }}
                                    </button>
                                    <button wire:click="delete({{ $a->id }})" wire:confirm="¿Eliminar «{{ $a->title }}»? Esta acción no se puede deshacer." class="block w-full px-4 py-2 text-left text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-700">Eliminar</button>
                                </div>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">No hay contenidos con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $articles->links() }}</div>
</div>
