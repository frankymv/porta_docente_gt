<div class="mx-auto max-w-6xl">
    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Usuarios</h1>
    <p class="text-sm text-slate-500">Consulta las cuentas registradas, cambia contraseñas o elimina usuarios.</p>

    {{-- Filtros --}}
    <div class="card mt-5 grid gap-3 sm:grid-cols-3">
        <input type="search" wire:model.live.debounce.350ms="search" placeholder="Buscar por nombre, correo o institución…" class="input sm:col-span-2">
        <select wire:model.live="role" class="input">
            <option value="">Todos los roles</option>
            <option value="admin">Administradores</option>
            @foreach (\App\Models\User::ROLES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    {{-- Tabla --}}
    <div class="card mt-4 overflow-x-auto p-0" wire:loading.class="opacity-60" wire:target="search,role">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/50">
                <tr>
                    <th class="px-4 py-3">Usuario</th>
                    <th class="px-4 py-3">Rol</th>
                    <th class="px-4 py-3 text-right">Contenidos</th>
                    <th class="px-4 py-3 text-right">Comunidad</th>
                    <th class="px-4 py-3">Registro</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($users as $u)
                    <tr wire:key="user-{{ $u->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/40 {{ $selectedId === $u->id ? 'bg-brand-50/60 dark:bg-brand-900/20' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ Str::upper(Str::substr($u->name, 0, 1)) }}</span>
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-slate-900 dark:text-white">
                                        {{ $u->name }}
                                        @if ($u->id === auth()->id()) <span class="text-xs font-normal text-slate-400">(tú)</span> @endif
                                    </p>
                                    <p class="truncate text-xs text-slate-500">{{ $u->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            {{ $u->role_label }}
                            @if ($u->is_admin) <span class="ml-1 rounded-full bg-brand-500 px-2 py-0.5 text-xs font-semibold text-white">Admin</span> @endif
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums">{{ $u->articles_count }}</td>
                        <td class="px-4 py-3 text-right tabular-nums" title="Publicaciones · comentarios">{{ $u->posts_count }} · {{ $u->comments_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <button wire:click="select({{ $u->id }})" class="font-semibold text-brand-600 hover:underline">Ver</button>
                            @if ($u->id !== auth()->id())
                                <button wire:click="delete({{ $u->id }})"
                                        wire:confirm="¿Eliminar a {{ $u->name }}? Se borrarán sus publicaciones y comentarios de la comunidad; sus contenidos (artículos) se conservan sin autor. Esta acción no se puede deshacer."
                                        class="ml-3 text-rose-600 hover:underline">Eliminar</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">No hay usuarios con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

    {{-- ============ Panel de detalle ============ --}}
    @if ($selected)
        <div class="fixed inset-0 z-40 flex justify-end" x-data x-on:keydown.escape.window="$wire.close()">
            <div class="absolute inset-0 bg-black/40" wire:click="close"></div>

            <aside class="relative z-10 flex h-full w-full max-w-md flex-col overflow-y-auto bg-white p-6 shadow-xl dark:bg-slate-900" wire:key="panel-{{ $selected->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="grid h-12 w-12 place-items-center rounded-full bg-brand-100 text-lg font-bold text-brand-700">{{ Str::upper(Str::substr($selected->name, 0, 1)) }}</span>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $selected->name }}</h2>
                            <p class="text-sm text-slate-500">{{ $selected->email }}</p>
                        </div>
                    </div>
                    <button wire:click="close" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Cerrar">✕</button>
                </div>

                <dl class="mt-6 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div><dt class="text-xs text-slate-500">Rol</dt><dd class="font-medium">{{ $selected->role_label }}{{ $selected->is_admin ? ' · Administrador' : '' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Registro</dt><dd class="font-medium">{{ $selected->created_at->format('d/m/Y H:i') }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Institución</dt><dd class="font-medium">{{ $selected->institution ?: '—' }}</dd></div>
                    <div><dt class="text-xs text-slate-500">País</dt><dd class="font-medium">{{ $selected->country ?: '—' }}</dd></div>
                    <div class="col-span-2"><dt class="text-xs text-slate-500">Áreas que enseña</dt><dd class="font-medium">{{ $selected->subjects ?: '—' }}</dd></div>
                    @if ($selected->bio)
                        <div class="col-span-2"><dt class="text-xs text-slate-500">Sobre mí</dt><dd class="whitespace-pre-line">{{ $selected->bio }}</dd></div>
                    @endif
                </dl>

                <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                    @foreach ([['Contenidos', $selected->articles_count], ['Publicaciones', $selected->posts_count], ['Comentarios', $selected->comments_count]] as [$label, $n])
                        <div class="rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
                            <p class="text-xl font-extrabold">{{ $n }}</p>
                            <p class="text-xs text-slate-500">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Cambiar contraseña --}}
                <form wire:submit="changePassword" class="mt-8 space-y-3 border-t border-slate-200 pt-6 dark:border-slate-800" x-data="{ show: false }">
                    <h3 class="font-bold">Cambiar contraseña</h3>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="new-password">Nueva contraseña</label>
                        <div class="relative">
                            <input id="new-password" :type="show ? 'text' : 'password'" wire:model="password" autocomplete="new-password" class="input pr-16">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-xs font-semibold text-brand-600" x-text="show ? 'Ocultar' : 'Mostrar'"></button>
                        </div>
                        @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium" for="new-password-confirm">Confirmar contraseña</label>
                        <input id="new-password-confirm" :type="show ? 'text' : 'password'" wire:model="password_confirmation" autocomplete="new-password" class="input">
                    </div>

                    <button class="btn-primary w-full" wire:loading.attr="disabled" wire:target="changePassword">
                        <span wire:loading.remove wire:target="changePassword">Guardar contraseña</span>
                        <span wire:loading wire:target="changePassword">Guardando…</span>
                    </button>
                    <p class="text-xs text-slate-500">Mínimo 8 caracteres. Comunícasela al usuario por un canal seguro.</p>
                </form>

                {{-- Eliminar --}}
                @if ($selected->id !== auth()->id())
                    <div class="mt-8 border-t border-slate-200 pt-6 dark:border-slate-800">
                        <h3 class="font-bold text-rose-600">Zona de peligro</h3>
                        <p class="mt-1 text-sm text-slate-500">Se borrarán sus publicaciones y comentarios de la comunidad. Sus contenidos se conservan sin autor.</p>
                        <button wire:click="delete({{ $selected->id }})"
                                wire:confirm="¿Eliminar definitivamente a {{ $selected->name }}? Esta acción no se puede deshacer."
                                class="btn-ghost mt-3 !border-rose-300 !text-rose-600">Eliminar usuario</button>
                    </div>
                @endif
            </aside>
        </div>
    @endif
</div>
