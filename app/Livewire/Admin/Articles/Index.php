<?php

namespace App\Livewire\Admin\Articles;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Contenidos')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $status = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(int $id): void
    {
        $article = Article::findOrFail($id);

        $article->update([
            'published_at' => $article->published_at ? null : now(),
        ]);

        $this->dispatch('notify', message: $article->published_at ? 'Contenido publicado' : 'Contenido pasado a borrador');
    }

    public function toggleFeatured(int $id): void
    {
        $article = Article::findOrFail($id);
        $article->update(['is_featured' => ! $article->is_featured]);
    }

    public function delete(int $id): void
    {
        $article = Article::findOrFail($id);

        // Elimina la portada solo si fue subida desde el panel (disco public)
        if ($article->cover_url && str_starts_with($article->cover_url, '/storage/')) {
            Storage::disk('public')->delete(substr($article->cover_url, strlen('/storage/')));
        }

        $article->delete();
        $this->dispatch('notify', message: 'Contenido eliminado');
    }

    public function render()
    {
        $term = trim($this->search);

        $articles = Article::with('category')
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->status === 'published', fn ($q) => $q->whereNotNull('published_at')->where('published_at', '<=', now()))
            ->when($this->status === 'scheduled', fn ($q) => $q->where('published_at', '>', now()))
            ->when($this->status === 'draft', fn ($q) => $q->whereNull('published_at'))
            ->when($term !== '', fn ($q) => $q->where(function ($w) use ($term) {
                $w->where('title', 'like', "%{$term}%")->orWhere('author_name', 'like', "%{$term}%");
            }))
            ->latest('updated_at')
            ->paginate(15);

        return view('livewire.admin.articles.index', compact('articles'));
    }
}
