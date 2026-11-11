<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Contenidos')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(as: 'categoria', except: '')]
    public string $category = '';

    #[Url(as: 'etiqueta', except: '')]
    public string $tag = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'category', 'tag'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'type', 'category', 'tag']);
        $this->resetPage();
    }

    public function render()
    {
        $term = trim($this->search);

        $articles = Article::published()
            ->with(['category', 'tags'])
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->category, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->category)))
            ->when($this->tag, fn ($q) => $q->whereHas('tags', fn ($t) => $t->where('slug', $this->tag)))
            ->when($term !== '', fn ($q) => $q->where(function ($w) use ($term) {
                $w->where('title', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->orWhere('author_name', 'like', "%{$term}%");
            }))
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.articles.index', [
            'articles' => $articles,
            'categories' => Category::withCount(['articles' => fn ($q) => $q->published()])->orderBy('name')->get(),
            'tags' => Tag::withCount('articles')->orderByDesc('articles_count')->take(20)->get(),
        ]);
    }
}
