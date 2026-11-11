<div x-data="readingProgress" @scroll.window.throttle.50ms="update()">
    {{-- Barra de progreso de lectura (Alpine) --}}
    <div class="fixed inset-x-0 top-16 z-30 h-1 bg-transparent">
        <div class="h-full bg-amber-400 transition-[width]" :style="`width: ${pct}%`"></div>
    </div>

    <article class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
        <nav class="text-sm text-slate-500">
            <a href="{{ route('articles.index') }}" wire:navigate class="hover:underline">Contenidos</a> /
            <a href="{{ route('articles.index', ['categoria' => $article->category->slug]) }}" wire:navigate class="hover:underline">{{ $article->category->name }}</a>
        </nav>

        <div class="mt-4 flex items-center gap-2 text-sm">
            <span class="badge">{{ $article->type_label }}</span>
            <span class="text-slate-500">{{ $article->reading_minutes }} min · {{ number_format($article->views) }} lecturas</span>
        </div>

        <h1 class="mt-3 text-3xl font-extrabold leading-tight text-slate-900 sm:text-4xl dark:text-white">{{ $article->title }}</h1>
        <p class="mt-3 text-sm text-slate-500">
            {{ $article->author_name ?? 'Equipo editorial' }} · {{ $article->published_at->translatedFormat('d \d\e F \d\e Y') }}
        </p>

        {{-- Acciones --}}
        <div class="mt-6 flex flex-wrap items-center gap-3" x-data="copyLink">
            <button wire:click="toggleSave" class="{{ $saved ? 'btn-primary' : 'btn-ghost' }}">
                {{ $saved ? '★ Guardado' : '☆ Guardar' }}
            </button>
            <button type="button" @click="copy()" class="btn-ghost">
                <span x-text="copied ? '¡Enlace copiado!' : 'Copiar enlace'"></span>
            </button>
            @if ($article->external_url)
                <a href="{{ $article->external_url }}" target="_blank" rel="noopener noreferrer" class="btn-ghost">Abrir recurso ↗</a>
            @endif
        </div>

        @if ($article->cover_url)
            <img src="{{ $article->cover_src }}" alt="Portada de {{ $article->title }}" class="mt-8 max-h-96 w-full rounded-2xl object-cover">
        @endif

        <p class="mt-8 rounded-2xl bg-brand-50 p-5 text-lg leading-8 text-brand-900 dark:bg-brand-900/40 dark:text-brand-100">{{ $article->excerpt }}</p>

        <div class="prose-article mt-6">
            {!! Str::markdown((string) $article->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>

        @if ($article->tags->isNotEmpty())
            <div class="mt-8 flex flex-wrap gap-2">
                @foreach ($article->tags as $tag)
                    <a href="{{ route('articles.index', ['etiqueta' => $tag->slug]) }}" wire:navigate
                       class="rounded-full border border-slate-300 px-3 py-1 text-xs hover:border-brand-500 dark:border-slate-700">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        {{-- Nota personal --}}
        @if ($saved)
            <div class="card mt-10">
                <h2 class="font-bold">Tus apuntes</h2>
                <p class="text-sm text-slate-500">Solo tú puedes verlos. Aparecen en Mi Espacio.</p>
                <textarea wire:model="note" rows="4" class="input mt-3" placeholder="Ideas para aplicar en tu clase…"></textarea>
                @error('note') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                <button wire:click="saveNote" class="btn-primary mt-3">Guardar nota</button>
            </div>
        @endif
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto mt-6 max-w-7xl px-4 sm:px-6">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white">Más en {{ $article->category->name }}</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($related as $r)
                    <x-article-card :article="$r" wire:key="r-{{ $r->id }}" />
                @endforeach
            </div>
        </section>
    @endif
</div>
