<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#5454e9">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'PortalDocenteGT') }}</title>
    <meta name="description" content="Artículos, documentos, asistencias tecnicas y comunidad para docentes y estudiantes">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('logo_portaldocente_gt.ico') }}">

    {{-- Evita el parpadeo del modo oscuro --}}
    <script>
        document.documentElement.classList.remove('dark');
        localStorage.removeItem('theme');
    </script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200">
    {{-- ============ NAVBAR ============ --}}
    <header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/10 bg-brand-900 text-white shadow">
        <div class="mx-auto flex h-16 max-w-7xlx items-center justify-between px-4 sm:px-6">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2 text-xl font-extrabold tracking-tight">
                <!-- Icono de la marca -->
                <img src="{{ asset('logo_portaldocente_gt.png') }}" alt="Logo" class="h-12   w-12 object-contain brightness-0 invert">

                <span>PortalDocenteGT</span>
            </a>

            {{-- Desktop --}}
            <nav class="hidden items-center gap-1 md:flex">
                @php
                    $links = [
                        ['Contenidos', 'articles.*', route('articles.index')],
                        ['Comunidad', 'community.*', route('community.index')],
                        ['Mi Espacio', 'space', route('space')],
                    ];
                @endphp
                @foreach ($links as [$label, $pattern, $url])
                    <a href="{{ $url }}" wire:navigate
                       class="rounded-lg px-3 py-2 text-sm font-medium transition hover:bg-white/15 {{ request()->routeIs($pattern) ? 'bg-white/20' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">


                @auth
                    <div x-data="{ menu: false }" class="relative hidden md:block">
                        <button @click="menu = !menu" @click.outside="menu = false" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium hover:bg-white/15">
                            <span class="grid h-7 w-7 place-items-center rounded-full bg-white/25 text-xs font-bold">
                                {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            {{ Str::before(auth()->user()->name, ' ') }}
                        </button>
                        <div x-show="menu" x-cloak x-transition
                             class="absolute right-0 mt-2 w-48 overflow-hidden rounded-xl bg-white py-1 text-sm text-slate-700 shadow-lg dark:bg-slate-800 dark:text-slate-200">
                            <a href="{{ route('space') }}" wire:navigate class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">Mi Espacio</a>
                            <a href="{{ route('space.articles.index') }}" wire:navigate class="block px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700">Mis contenidos</a>
                            @if (auth()->user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}" wire:navigate class="block px-4 py-2 font-semibold text-brand-600 hover:bg-slate-100 dark:text-brand-100 dark:hover:bg-slate-700">Administración</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="block w-full px-4 py-2 text-left hover:bg-slate-100 dark:hover:bg-slate-700">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="hidden rounded-xl bg-white px-4 py-2 text-sm font-semibold text-brand-600 hover:bg-brand-50 md:inline-block">
                        Iniciar sesión
                    </a>
                @endauth

                {{-- Botón móvil --}}
                <button @click="open = !open" class="rounded-lg p-2 hover:bg-white/15 md:hidden" aria-label="Menú">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M6 18L18 6"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Menú móvil --}}
        <div x-show="open" x-cloak x-transition class="border-t border-white/15 md:hidden">
            <div class="space-y-1 px-4 py-3">
                @foreach ($links as [$label, $pattern, $url])
                    <a href="{{ $url }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm font-medium hover:bg-white/15">{{ $label }}</a>
                @endforeach
                @auth
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button class="block w-full rounded-lg px-3 py-2 text-left text-sm font-medium hover:bg-white/15">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="block rounded-lg px-3 py-2 text-sm font-semibold hover:bg-white/15">Iniciar sesión</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ============ CONTENIDO ============ --}}
    <main>
        {{ $slot }}
    </main>

    {{-- ============ TOASTS ============ --}}
    <div x-data="toasts" @notify.window="add($event.detail.message, $event.detail.type ?? 'ok')"
         class="pointer-events-none fixed bottom-4 right-4 z-50 space-y-2">
        @if (session('ok'))
            <div x-init="add(@js(session('ok')))"></div>
        @endif
        <template x-for="t in items" :key="t.id">
            <div x-transition class="pointer-events-auto rounded-xl px-4 py-3 text-sm font-medium text-white shadow-lg"
                 :class="t.type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'" x-text="t.message"></div>
        </template>
    </div>

    {{-- ============ FOOTER ============ --}}
    <footer class="mt-20 bg-brand-900 text-slate-300">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-4">
            <div class="md:col-span-2">
                <p class="text-xl font-extrabold text-white">PortalDocenteGT</p>
                <p class="mt-3 max-w-md text-sm">
                    Plataforma educativa gratuita con artículos, documentos y asistencia técnica para docentes estudiantes.
                </p>
            </div>
            <div>
                <p class="mb-3 text-sm font-semibold text-white">Contenidos</p>
                <ul class="space-y-2 text-sm">
                    @foreach (\App\Models\Article::TYPES as $key => $label)
                        <li><a class="hover:text-white" href="{{ route('articles.index', ['type' => $key]) }}" wire:navigate>{{ $label }}s</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="mb-3 text-sm font-semibold text-white">Información</p>
                <ul class="space-y-2 text-sm">
                    <li><a class="hover:text-white" href="{{ route('community.index') }}" wire:navigate>Comunidad</a></li>
                    <li><a class="hover:text-white" href="{{ route('register') }}" wire:navigate>Crear cuenta</a></li>
                </ul>
            </div>
        </div>
         <div class="border-t border-white/10 py-4 text-center text-xs">

            <span class="inline-flex items-center gap-1 text-gray-400">
                <strong>2026- PROYECTO GRUPO 2 - TIC </strong>
            </span>
            <br>
            <span class="inline-flex items-center gap-1 text-gray-400">
                <strong>Franky Emmanuel Mejía Vicente</strong>

                Desarrollador
            </span>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
