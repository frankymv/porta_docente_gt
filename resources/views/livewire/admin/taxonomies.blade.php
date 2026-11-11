<div class="mx-auto max-w-3xl">
    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Categorías y etiquetas</h1>
    <p class="text-sm text-slate-500">Organiza los contenidos del portal.</p>

    <div class="mt-5 flex gap-2 border-b border-slate-200 dark:border-slate-800">
        @foreach (['categories' => 'Categorías', 'tags' => 'Etiquetas'] as $key => $label)
            <button wire:click="setTab('{{ $key }}')"
                    class="border-b-2 px-4 py-2.5 text-sm font-semibold {{ $tab === $key ? 'border-brand-500 text-brand-600 dark:text-brand-100' : 'border-transparent text-slate-500' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Formulario crear / editar --}}
    <form wire:submit="save" class="card mt-5 flex flex-wrap items-start gap-3">
        <div class="min-w-0 flex-1">
            <input type="text" wire:model="name" maxlength="80" class="input"
                   placeholder="{{ $editingId ? 'Nuevo nombre' : ($tab === 'tags' ? 'Nueva etiqueta' : 'Nueva categoría') }}">
            @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        </div>
        <button class="btn-primary">{{ $editingId ? 'Guardar' : 'Agregar' }}</button>
        @if ($editingId)
            <button type="button" wire:click="cancel" class="btn-ghost">Cancelar</button>
        @endif
    </form>

    {{-- Listado --}}
    <div class="card mt-4 divide-y divide-slate-100 p-0 dark:divide-slate-800">
        @forelse ($items as $item)
            <div class="flex items-center justify-between gap-3 px-5 py-3 {{ $editingId === $item->id ? 'bg-brand-50 dark:bg-brand-900/30' : '' }}" wire:key="{{ $tab }}-{{ $item->id }}">
                <div>
                    <p class="font-medium">{{ $item->name }}</p>
                    <p class="text-xs text-slate-500">/{{ $item->slug }} · {{ $item->articles_count }} {{ $item->articles_count === 1 ? 'contenido' : 'contenidos' }}</p>
                </div>
                <div class="flex gap-3 text-sm">
                    <button wire:click="edit({{ $item->id }})" class="font-semibold text-brand-600 hover:underline">Editar</button>
                    <button wire:click="delete({{ $item->id }})" wire:confirm="¿Eliminar «{{ $item->name }}»?" class="text-rose-600 hover:underline">Eliminar</button>
                </div>
            </div>
        @empty
            <p class="px-5 py-10 text-center text-slate-500">Todavía no hay elementos.</p>
        @endforelse
    </div>
</div>
