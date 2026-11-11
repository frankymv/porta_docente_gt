<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Contenidos</h1>
    <p class="mt-1 text-slate-500">Artículos, documentos, webinars y podcast para tu práctica docente.</p>

    {{-- Tabs por tipo --}}
    <div class="mt-6 flex gap-2 overflow-x-auto border-b border-slate-200 dark:border-slate-800">
        <button wire:click="$set('type', '')"
                class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold {{ $type === '' ? 'border-brand-500 text-brand-600 dark:text-brand-100' : 'border-transparent text-slate-500' }}">
            Todo
        </button>
        @foreach (\App\Models\Article::TYPES as $key => $label)
            <button wire:click="$set('type', '{{ $key }}')"
                    class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold {{ $type === $key ? 'border-brand-500 text-brand-600 dark:text-brand-100' : 'border-transparent text-slate-500' }}">
                {{ $label }}s
            </button>
        @endforeach
    </div>

    <div class="mt-8 grid gap-8 lg:grid-cols-4">
        {{-- Sidebar --}}
        <aside class="space-y-6 lg:col-span-1" x-data="{ filters: false }">
            <input wire:model.live.debounce.350ms="search" type="search" placeholder="Buscar contenidos…" class="input">

            <button @click="filters = !filters" class="btn-ghost w-full lg:hidden">
                <span x-text="filters ? 'Ocultar filtros' : 'Mostrar filtros'"></span>
            </button>

            <div class="space-y-6" :class="filters ? 'block' : 'hidden lg:block'">
                <div>
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wider text-slate-500">Categorías</h2>
                    <ul class="space-y-1">
                        @foreach ($categories as $c)
                            <li>
                                <button wire:click="$set('category', '{{ $category === $c->slug ? '' : $c->slug }}')"
                                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800 {{ $category === $c->slug ? 'bg-brand-50 font-semibold text-brand-700 dark:bg-brand-900 dark:text-brand-100' : '' }}">
                                    <span>{{ $c->name }}</span>
                                    <span class="text-xs text-slate-400">{{ $c->articles_count }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="mb-2 text-sm font-bold uppercase tracking-wider text-slate-500">Etiquetas</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tags as $t)
                            <button wire:click="$set('tag', '{{ $tag === $t->slug ? '' : $t->slug }}')"
                                    class="rounded-full border px-3 py-1 text-xs {{ $tag === $t->slug ? 'border-brand-500 bg-brand-500 text-white' : 'border-slate-300 hover:border-brand-500 dark:border-slate-700' }}">
                                #{{ $t->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if ($search || $type || $category || $tag)
                    <button wire:click="clearFilters" class="text-sm font-semibold text-rose-600 hover:underline">Limpiar filtros</button>
                @endif
            </div>
        </aside>

        {{-- Resultados --}}
        <section class="lg:col-span-3">
            <div wire:loading.delay class="mb-4 text-sm text-slate-500">Buscando…</div>

            @if ($articles->count())
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3" wire:loading.class="opacity-50">
                    @foreach ($articles as $article)
                        <x-article-card :article="$article" wire:key="a-{{ $article->id }}" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $articles->links() }}</div>
            @else
                <div class="card py-16 text-center">
                    <p class="text-lg font-semibold">No encontramos resultados</p>
                    <p class="mt-1 text-sm text-slate-500">Prueba con otros términos o limpia los filtros.</p>
                </div>
            @endif
        </section>
    </div>
</div>
