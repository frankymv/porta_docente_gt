<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}Administración · {{ config('app.name', 'PortalDocenteGT') }}</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet">
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-200">
    <div x-data="{ sidebar: false }" class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'"
               class="fixed inset-y-0 left-0 z-40 w-64 transform bg-brand-900 text-slate-200 transition md:static md:translate-x-0">
            <div class="flex h-16 items-center px-6 text-lg font-extrabold text-white">
                PortalDocenteGT
                <span class="ml-2 rounded bg-white/15 px-2 py-0.5 text-xs font-semibold">Admin</span>
            </div>

            <nav class="space-y-1 px-3 py-4 text-sm font-medium">
                @php
                    $items = [
                        ['Resumen', 'admin.dashboard', route('admin.dashboard'), '📊'],
                        ['Contenidos', 'admin.articles.*', route('admin.articles.index'), '📰'],
                        ['Categorías y etiquetas', 'admin.taxonomies', route('admin.taxonomies'), '🏷️'],
                        ['Comunidad', 'admin.posts', route('admin.posts'), '💬'],
                        ['Usuarios', 'admin.users', route('admin.users'), '💬'],
                    ];
                @endphp
                @foreach ($items as [$label, $pattern, $url, $icon])
                    <a href="{{ $url }}" wire:navigate @click="sidebar = false"
                       class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition hover:bg-white/10 {{ request()->routeIs($pattern) ? 'bg-white/15 text-white' : '' }}">
                        <span>{{ $icon }}</span> {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="absolute inset-x-0 bottom-0 space-y-1 border-t border-white/10 p-3 text-sm">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">↗ Ver el sitio</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="block w-full rounded-lg px-3 py-2 text-left hover:bg-white/10">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-30 bg-black/40 md:hidden"></div>

        {{-- Contenido --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 dark:border-slate-800 dark:bg-slate-900 sm:px-6">
                <button @click="sidebar = true" class="rounded-lg p-2 hover:bg-slate-100 md:hidden dark:hover:bg-slate-800" aria-label="Menú">☰</button>
                <p class="hidden text-sm text-slate-500 md:block">Panel de administración</p>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Toasts --}}
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

    @livewireScripts
</body>
</html>
