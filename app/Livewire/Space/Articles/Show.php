<?php

namespace App\Livewire\Space\Articles;

use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Article $article;

    public function mount(Article $article): void
    {
        abort_unless($article->user_id === Auth::id(), 403, 'Solo puedes ver tus propios contenidos aquí.');

        $this->article = $article->load(['category', 'tags']);
    }

    public function togglePublish(): void
    {
        $this->authorizeOwner();

        $this->article->update(['published_at' => $this->article->published_at ? null : now()]);

        $this->dispatch('notify', message: $this->article->published_at ? 'Contenido publicado' : 'Contenido pasado a borrador');
    }

    public function delete()
    {
        $this->authorizeOwner();

        if ($this->article->cover_url && str_starts_with($this->article->cover_url, '/storage/')) {
            Storage::disk('public')->delete(substr($this->article->cover_url, strlen('/storage/')));
        }

        $this->article->delete();
        session()->flash('ok', 'Contenido eliminado');

        return $this->redirectRoute('space.articles.index', navigate: true);
    }

    private function authorizeOwner(): void
    {
        abort_unless($this->article->user_id === Auth::id(), 403);
    }

    public function render()
    {
        return view('livewire.shared.article-detail', [
            'backUrl' => route('space.articles.index'),
            'editUrl' => route('space.articles.edit', $this->article),
            'padded' => true,
        ])->title($this->article->title);
    }
}
