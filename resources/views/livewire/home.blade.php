<div>
    {{-- ============ HERO ============ --}}
    <section class="bg-gradient-to-br from-brand-500 via-brand-600 to-brand-900 text-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:py-24">
            <p class="text-sm font-semibold uppercase tracking-widest text-amber-300">PortalDocenteGT</p>
            <h1 class="mt-3 max-w-3xl text-4xl font-extrabold leading-tight sm:text-5xl">
                Recursos, artículos y comunidad para transformar el aula
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-white/85">
                Artículos, documentos, asistencias tecnicas y comunidad para docentes y estudiantes.
            </p>

            <form wire:submit="goSearch"
                class="mt-8 flex max-w-2xl gap-2 rounded-xl bg-white p-2 shadow-sm">

                <input
                    wire:model="search"
                    type="search"
                    placeholder="Rubrica en word..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-black placeholder-gray-500 focus:border-amber-400 focus:ring-2 focus:ring-amber-300">

                <button
                    type="submit"
                    class="rounded-xl border border-amber-400 bg-amber-300 px-6 py-3 font-semibold text-black hover:bg-amber-200">
                    Buscar
                </button>

            </form>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('articles.index') }}" wire:navigate class="btn bg-white text-brand-700 hover:bg-brand-50">Explorar contenidos</a>
                <a href="{{ route('community.index') }}" wire:navigate class="btn border border-white/40 text-white hover:bg-white/10">Ir a la comunidad</a>
            </div>
        </div>
    </section>

    {{-- ============ ESTADÍSTICAS ============ --}}
    <section class="mx-auto -mt-8 max-w-7xl px-4 sm:px-6">
        <div class="grid grid-cols-2 gap-5 md:grid-cols-5">
            @foreach ([
                [$stats['articles'], 'artículos publicados'],
                [$stats['documents'], 'documentos'],
                [$stats['asistencia'], 'asistencia técnica'],
                [$stats['webinars'], 'webinars'],
                [$stats['members'], 'docentes en la comunidad'],
            ] as [$n, $label])
                <div class="card text-center">
                    <p class="text-3xl font-extrabold text-brand-600 dark:text-brand-100">{{ number_format($n) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============ NOVEDADES (tabs con Alpine) ============ --}}
    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6" x-data="{ tab: 'articulo' }">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-brand-500">Contenido</p>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Novedades del portal</h2>
            </div>
            <a href="{{ route('articles.index') }}" wire:navigate class="text-sm font-semibold text-brand-600 hover:underline dark:text-brand-100">Ver todo →</a>
        </div>

        <div class="mt-6 flex gap-2 overflow-x-auto border-b border-slate-200 dark:border-slate-800">
            @foreach (\App\Models\Article::TYPES as $key => $label)
                <button @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}' ? 'border-brand-500 text-brand-600 dark:text-brand-100' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
                        class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold transition">
                    {{ $label }}s
                </button>
            @endforeach
        </div>

        @foreach (\App\Models\Article::TYPES as $key => $label)
            <div x-show="tab === '{{ $key }}'" x-cloak x-transition.opacity class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse (($latest[$key] ?? collect())->take(4) as $article)
                    <x-article-card :article="$article" />
                @empty
                    <p class="col-span-full py-8 text-center text-slate-500">Aún no hay {{ Str::lower($label) }}s publicados.</p>
                @endforelse
            </div>
        @endforeach
    </section>

    {{-- ============ COMUNIDAD ============ --}}
    <section class="mx-auto mt-20 max-w-7xl px-4 sm:px-6">
        <div class="flex items-end justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-brand-500">Comunidad</p>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Lo que los docentes comparten y guardan</h2>
            </div>
            <a href="{{ route('community.index') }}" wire:navigate class="hidden text-sm font-semibold text-brand-600 hover:underline sm:block dark:text-brand-100">Ver la comunidad →</a>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            <div class="card lg:col-span-1">
                <h3 class="font-bold text-slate-900 dark:text-white">Lo más guardado</h3>
                <ol class="mt-4 space-y-3">
                    @foreach ($mostSaved as $i => $a)
                        <li class="flex gap-3">
                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full bg-brand-50 text-sm font-bold text-brand-700 dark:bg-brand-900 dark:text-brand-100">{{ $i + 1 }}</span>
                            <div>
                                <a href="{{ route('articles.show', $a) }}" wire:navigate class="text-sm font-semibold hover:text-brand-600">{{ $a->title }}</a>
                                <p class="text-xs text-slate-500">{{ $a->saved_by_count }} {{ Str::plural('docente', $a->saved_by_count) }} lo guardaron</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="space-y-4 lg:col-span-2">
                @foreach ($recentPosts as $post)
                    <a href="{{ route('community.show', $post) }}" wire:navigate class="card block transition hover:border-brand-500">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="badge">{{ $post->kind_label }}</span>
                            <span>{{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-slate-900 dark:text-white">{{ $post->title }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-slate-600 dark:text-slate-400">{{ $post->body }}</p>
                        <p class="mt-3 text-xs text-slate-500">♥ {{ $post->likes_count }} · 💬 {{ $post->comments_count }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    @guest
        <section class="mx-auto mt-20 max-w-5xl px-4 sm:px-6">
            <div class="rounded-3xl bg-brand-500 p-10 text-center text-white">
                <h2 class="text-3xl font-extrabold">Crea tu espacio como docente</h2>
                <p class="mx-auto mt-3 max-w-xl text-white/85">Guarda artículos con tus notas, publica en la comunidad y organiza tus recursos.</p>
                <a href="{{ route('register') }}" wire:navigate class="btn mt-6 bg-white text-brand-700 hover:bg-brand-50">Crear cuenta gratis</a>
            </div>
        </section>
    @endguest
</div>
