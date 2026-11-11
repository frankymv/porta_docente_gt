<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6" x-data="{ tab: 'saved' }">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <span class="grid h-16 w-16 place-items-center rounded-2xl bg-brand-500 text-2xl font-extrabold text-white">
                {{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}
            </span>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Hola, {{ Str::before(auth()->user()->name, ' ') }}</h1>
                <p class="text-sm text-slate-500">{{ auth()->user()->role_label }}{{ auth()->user()->institution ? ' · '.auth()->user()->institution : '' }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('space.articles.index') }}" wire:navigate class="btn-ghost">Mis contenidos</a>
            <a href="{{ route('space.articles.create') }}" wire:navigate class="btn-primary">+ Publicar contenido</a>
        </div>
    </div>

    <div class="mt-8 flex gap-2 overflow-x-auto border-b border-slate-200 dark:border-slate-800">
        @foreach (['saved' => 'Guardados ('.$saved->count().')', 'posts' => 'Mis publicaciones ('.$posts->count().')', 'profile' => 'Perfil'] as $key => $label)
            <button @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'border-brand-500 text-brand-600 dark:text-brand-100' : 'border-transparent text-slate-500'"
                    class="whitespace-nowrap border-b-2 px-4 py-2.5 text-sm font-semibold">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Guardados --}}
    <section x-show="tab === 'saved'" class="mt-6 space-y-4">
        @forelse ($saved as $a)
            <div class="card" wire:key="s-{{ $a->id }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="badge">{{ $a->category->name }}</span>
                        <a href="{{ route('articles.show', $a) }}" wire:navigate class="mt-2 block font-bold hover:text-brand-600">{{ $a->title }}</a>
                    </div>
                    <button wire:click="unsave({{ $a->id }})" class="text-sm text-slate-400 hover:text-rose-600">Quitar</button>
                </div>
                @if ($a->pivot->note)
                    <p class="mt-3 rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-900/20 dark:text-amber-100">📝 {{ $a->pivot->note }}</p>
                @endif
            </div>
        @empty
            <div class="card py-12 text-center text-slate-500">
                Aún no has guardado contenidos.
                <a href="{{ route('articles.index') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Explorar contenidos</a>
            </div>
        @endforelse
    </section>

    {{-- Publicaciones --}}
    <section x-show="tab === 'posts'" x-cloak class="mt-6 space-y-4">
        @forelse ($posts as $p)
            <a href="{{ route('community.show', $p) }}" wire:navigate class="card block hover:border-brand-500" wire:key="p-{{ $p->id }}">
                <span class="badge">{{ $p->kind_label }}</span>
                <p class="mt-2 font-bold">{{ $p->title }}</p>
                <p class="mt-1 text-xs text-slate-500">♥ {{ $p->likes_count }} · 💬 {{ $p->comments_count }} · {{ $p->created_at->diffForHumans() }}</p>
            </a>
        @empty
            <div class="card py-12 text-center text-slate-500">
                No has publicado nada todavía.
                <a href="{{ route('community.index') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Ir a la comunidad</a>
            </div>
        @endforelse
    </section>

    {{-- Perfil --}}
    <section x-show="tab === 'profile'" x-cloak class="mt-6">
        <form wire:submit="saveProfile" class="card grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Nombre</label>
                <input type="text" wire:model="name" class="input">
                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Rol</label>
                <select wire:model="role" class="input">
                    @foreach (\App\Models\User::ROLESUSER as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Institución</label>
                <input type="text" wire:model="institution" class="input">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">País</label>
                <input type="text" wire:model="country" class="input">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium">Áreas que enseñas</label>
                <input type="text" wire:model="subjects" placeholder="Matemáticas, Tecnología…" class="input">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-medium">Sobre mí</label>
                <textarea wire:model="bio" rows="4" class="input"></textarea>
                @error('bio') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <button class="btn-primary" wire:loading.attr="disabled">Guardar perfil</button>
            </div>
        </form>
    </section>
</div>
