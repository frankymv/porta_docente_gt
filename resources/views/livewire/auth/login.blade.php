<div class="mx-auto flex min-h-[70vh] max-w-md items-center px-4 py-12">
    <div class="card w-full p-8">
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Iniciar sesión</h1>
        <p class="mt-1 text-sm text-slate-500">Accede a tu espacio, tus guardados y la comunidad.</p>

        <form wire:submit="login" class="mt-6 space-y-4" x-data="{ show: false }">
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Correo electrónico</label>
                <input id="email" type="email" wire:model="email" autocomplete="email" autofocus class="input">
                @error('email') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Contraseña</label>
                <div class="relative">
                    <input id="password" :type="show ? 'text' : 'password'" wire:model="password" autocomplete="current-password" class="input pr-16">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 text-xs font-semibold text-brand-600" x-text="show ? 'Ocultar' : 'Mostrar'"></button>
                </div>
                @error('password') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-brand-500">
                Mantener sesión iniciada
            </label>

            <button class="btn-primary w-full" wire:loading.attr="disabled">
                <span wire:loading.remove>Entrar</span>
                <span wire:loading>Verificando…</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" wire:navigate class="font-semibold text-brand-600 hover:underline">Regístrate gratis</a>
        </p>
    </div>
</div>
