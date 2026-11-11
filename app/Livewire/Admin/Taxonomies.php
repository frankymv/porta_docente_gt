<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Categorías y etiquetas')]
class Taxonomies extends Component
{
    /** categories | tags */
    public string $tab = 'categories';

    public string $name = '';

    public $editingId = null;

    public function setTab(string $tab): void
    {
        $this->tab = $tab === 'tags' ? 'tags' : 'categories';
        $this->cancel();
    }

    private function modelClass(): string
    {
        return $this->tab === 'tags' ? Tag::class : Category::class;
    }

    public function save(): void
    {
        $this->validate(['name' => ['required', 'string', 'max:80']], [
            'name.required' => 'Escribe un nombre.',
        ]);

        $model = $this->modelClass();
        $slug = Str::slug($this->name);

        $exists = $model::where('slug', $slug)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->exists();

        if ($slug === '' || $exists) {
            $this->addError('name', 'Ya existe un elemento con ese nombre.');

            return;
        }

        if ($this->editingId) {
            $model::findOrFail($this->editingId)->update(['name' => trim($this->name), 'slug' => $slug]);
            $this->dispatch('notify', message: 'Actualizado');
        } else {
            $model::create(['name' => trim($this->name), 'slug' => $slug]);
            $this->dispatch('notify', message: 'Creado');
        }

        $this->cancel();
    }

    public function edit(int $id): void
    {
        $item = $this->modelClass()::findOrFail($id);
        $this->editingId = $item->id;
        $this->name = $item->name;
        $this->resetErrorBag();
    }

    public function cancel(): void
    {
        $this->reset(['name', 'editingId']);
        $this->resetErrorBag();
    }

    public function delete(int $id): void
    {
        $item = $this->modelClass()::findOrFail($id);

        // Una categoría con contenidos NO se puede borrar (la FK eliminaría los artículos)
        if ($item instanceof Category && $item->articles()->exists()) {
            $this->dispatch('notify', message: 'No se puede eliminar: la categoría tiene contenidos asociados.', type: 'error');

            return;
        }

        $item->delete();
        $this->cancel();
        $this->dispatch('notify', message: 'Eliminado');
    }

    public function render()
    {
        $items = $this->tab === 'tags'
            ? Tag::withCount('articles')->orderBy('name')->get()
            : Category::withCount('articles')->orderBy('name')->get();

        return view('livewire.admin.taxonomies', compact('items'));
    }
}
