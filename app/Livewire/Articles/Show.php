<?php

namespace App\Livewire\Articles;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Article $article;

    #[Validate('nullable|string|max:1000')]
    public string $note = '';

    public bool $saved = false;

    public function mount(Article $article): void
    {
        abort_unless($article->published_at && $article->published_at->isPast(), 404);

        $this->article = $article->load(['category', 'tags']);
        $article->increment('views');

        if (Auth::check()) {
            $pivot = Auth::user()->savedArticles()->where('articles.id', $article->id)->first()?->pivot;
            $this->saved = (bool) $pivot;
            $this->note = $pivot?->note ?? '';
        }
    }

    public function toggleSave(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        if ($this->saved) {
            Auth::user()->savedArticles()->detach($this->article->id);
            $this->saved = false;
            $this->note = '';
            $this->dispatch('notify', message: 'Quitado de tus guardados');
        } else {
            Auth::user()->savedArticles()->attach($this->article->id);
            $this->saved = true;
            $this->dispatch('notify', message: 'Guardado en Mi Espacio');
        }
    }

    public function saveNote(): void
    {
        abort_unless(Auth::check() && $this->saved, 403);
        $this->validate();

        Auth::user()->savedArticles()->updateExistingPivot($this->article->id, ['note' => $this->note ?: null]);
        $this->dispatch('notify', message: 'Nota guardada');
    }

    public function render()
    {
        $related = Article::published()
            ->with('category')
            ->where('id', '!=', $this->article->id)
            ->where('category_id', $this->article->category_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('livewire.articles.show', ['related' => $related])
            ->title($this->article->title);
    }
}
