<div class="mx-auto flex min-h-[70vh] max-w-md items-center px-4 py-12">
    <div class="card w-full p-8">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Crear cuenta</h1>
        <p class="mt-1 text-sm text-slate-500">Gratis. Guarda contenidos y participa en la comunidad.</p>

        <form wire:submit="register" class="mt-6 space-y-4">
            <div>
                <label class="mb-1 block text-sm font-medium" for="name">Nombre completo</label>
                <input id="name" type="text" wire:model="name" autocomplete="name" class="input">
                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium" for="email">Correo electrónico</label>
                <input id="email" type="email" wire:model.blur="email" autocomplete="email" class="input">
                @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium" for="role">Soy</label>
                    <select id="role" wire:model="role" class="input">
                        @foreach (\App\Models\User::ROLESUSER as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium" for="institution">Institución <span class="text-slate-400">(opcional)</span></label>
                    <input id="institution" type="text" wire:model="institution" class="input">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium" for="password">Contraseña</label>
                <input id="password" type="password" wire:model="password" autocomplete="new-password" class="input">
                @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium" for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password" class="input">
            </div>

            <button class="btn-primary w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Crear cuenta</span>
                <span wire:loading>Creando…</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Inicia sesión</a>
        </p>
    </div>
</div>
