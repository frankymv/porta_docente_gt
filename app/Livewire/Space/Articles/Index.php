<?php

namespace App\Livewire\Space\Articles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Mis contenidos')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(int $id): void
    {
        // Solo entre MIS contenidos: si el id es de otro usuario, responde 404
        $article = Auth::user()->articles()->findOrFail($id);

        $article->update(['published_at' => $article->published_at ? null : now()]);

        $this->dispatch('notify', message: $article->published_at ? 'Contenido publicado' : 'Contenido pasado a borrador');
    }

    public function delete(int $id): void
    {
        $article = Auth::user()->articles()->findOrFail($id);

        if ($article->cover_url && str_starts_with($article->cover_url, '/storage/')) {
            Storage::disk('public')->delete(substr($article->cover_url, strlen('/storage/')));
        }

        $article->delete();
        $this->dispatch('notify', message: 'Contenido eliminado');
    }

    public function render()
    {
        $term = trim($this->search);

        $articles = Auth::user()->articles()
            ->with('category')
            ->when($this->status === 'published', fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now()))
            ->when($this->status === 'scheduled', fn ($q) => $q->where('published_at', '>', now()))
            ->when($this->status === 'draft', fn ($q) => $q->whereNull('published_at'))
            ->when($term !== '', fn ($q) => $q->where('title', 'like', "%{$term}%"))
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.space.articles.index', compact('articles'));
    }
}
