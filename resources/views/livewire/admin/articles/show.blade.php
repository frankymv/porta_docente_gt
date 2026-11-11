<div class="mx-auto max-w-4xl">
    <a href="{{ route('admin.articles.index') }}" wire:navigate class="text-sm text-slate-500 hover:underline">← Volver a articulos</a>

    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            @if (! $article->published_at)
                <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-xs font-semibold text-slate-700">Borrador</span>
            @elseif ($article->published_at->isFuture())
                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Programado · {{ $article->published_at->format('d/m/Y H:i') }}</span>
            @else
                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Publicado</span>
            @endif
            @if ($article->is_featured) <span class="badge">⭐ Destacado</span> @endif
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.articles.edit', $article) }}" wire:navigate class="btn-primary">Editar</a>
            <button wire:click="togglePublish" class="btn-ghost">{{ $article->published_at ? 'Pasar a borrador' : 'Publicar ahora' }}</button>
            @if ($article->published_at && $article->published_at->isPast())
                <a href="{{ route('articles.show', $article) }}" target="_blank" class="btn-ghost">Ver en el sitio ↗</a>
            @endif
            <button wire:click="delete" wire:confirm="¿Eliminar «{{ $article->title }}»? Esta acción no se puede deshacer." class="btn-ghost !border-rose-300 !text-rose-600">Eliminar</button>
        </div>
    </div>

    <article class="card mt-5 !p-0 overflow-hidden">
        @if ($article->cover_url)
            <img src="{{ $article->cover_url }}" alt="Portada de {{ $article->title }}" class="h-64 w-full object-cover">
        @endif

        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <span class="badge">{{ $article->type_label }}</span>
                <span class="badge">{{ $article->category->name }}</span>
                <span class="text-slate-500">{{ $article->reading_minutes }} min · {{ number_format($article->views) }} lecturas</span>
            </div>

            <h1 class="mt-3 text-3xl font-extrabold leading-tight text-slate-900 dark:text-white">{{ $article->title }}</h1>
            <p class="mt-2 text-sm text-slate-500">
                {{ $article->author_name ?? 'Sin autor' }} · actualizado {{ $article->updated_at->diffForHumans() }} · /articulos/{{ $article->slug }}
            </p>

            <p class="mt-6 rounded-2xl bg-brand-50 p-4 text-slate-800 dark:bg-brand-900/40 dark:text-brand-100">{{ $article->excerpt }}</p>

            @if ($article->external_url)
                <p class="mt-4 text-sm">
                    Enlace externo:
                    <a href="{{ $article->external_url }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-600 underline">{{ $article->external_url }}</a>
                </p>
            @endif

            <div class="prose-article mt-6">
                {!! Str::markdown((string) ($article->body ?: '*Este articulo aún no tiene texto.*'), ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
            </div>

            @if ($article->tags->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($article->tags as $tag)
                        <span class="rounded-full border border-slate-300 px-3 py-1 text-xs dark:border-slate-700">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </article>
</div>
