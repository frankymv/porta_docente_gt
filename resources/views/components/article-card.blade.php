@props(['article'])

<a href="{{ route('articles.show', $article) }}" wire:navigate {{ $attributes }}
   class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900">
    <div class="h-36 bg-gradient-to-br from-brand-500 to-brand-900">
        @if ($article->cover_url)
            <img src="{{ $article->cover_src }}" alt="" loading="lazy" class="h-full w-full object-cover">
        @else
            <div class="grid h-full place-items-center text-4xl text-white/70">
                {{ ['articulo' => '📰', 'documento' => '📄', 'webinar' => '🎥', 'podcast' => '🎧'][$article->type] ?? '📚' }}
            </div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-center gap-2 text-xs">
            <span class="badge">{{ $article->category->name }}</span>
            <span class="text-slate-500">{{ $article->type_label }} · {{ $article->reading_minutes }} min</span>
        </div>
        <h3 class="mt-2 font-bold leading-snug text-slate-900 group-hover:text-brand-600 dark:text-white">{{ $article->title }}</h3>
        <p class="mt-2 line-clamp-3 flex-1 text-sm text-slate-600 dark:text-slate-400">{{ $article->excerpt }}</p>
        <p class="mt-3 text-xs text-slate-500">{{ $article->published_at?->translatedFormat('d M Y') }}</p>
    </div>
</a>
