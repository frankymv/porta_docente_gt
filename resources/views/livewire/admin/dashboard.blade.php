<div class="mx-auto max-w-6xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Resumen</h1>
            <p class="text-sm text-slate-500">Estado general del portal.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" wire:navigate class="btn-primary">+ Nuevo contenido</a>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ($stats as [$label, $value, $icon])
            <div class="card">
                <p class="text-2xl">{{ $icon }}</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">{{ number_format($value) }}</p>
                <p class="text-sm text-slate-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="font-bold">Últimos cambios</h2>
                <a href="{{ route('admin.articles.index') }}" wire:navigate class="text-sm font-semibold text-brand-600 hover:underline">Ver todos</a>
            </div>
            <ul class="mt-3 divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($latest as $a)
                    <li class="flex items-center justify-between gap-3 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.articles.show', $a) }}" wire:navigate class="block truncate font-medium hover:text-brand-600">{{ $a->title }}</a>
                            <p class="text-xs text-slate-500">{{ $a->type_label }} · {{ $a->category->name }} · {{ $a->updated_at->diffForHumans() }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ ! $a->published_at ? 'bg-slate-200 text-slate-700' : ($a->published_at->isFuture() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                            {{ ! $a->published_at ? 'Borrador' : ($a->published_at->isFuture() ? 'Programado' : 'Publicado') }}
                        </span>
                    </li>
                @empty
                    <li class="py-6 text-center text-slate-500">Aún no hay contenidos.</li>
                @endforelse
            </ul>
        </div>

        <div class="card">
            <h2 class="font-bold">Más leídos</h2>
            <ol class="mt-3 space-y-3">
                @foreach ($popular as $i => $a)
                    <li class="flex gap-3 text-sm">
                        <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-brand-50 text-xs font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-100">{{ $i + 1 }}</span>
                        <div>
                            <p class="font-medium">{{ $a->title }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($a->views) }} lecturas</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
