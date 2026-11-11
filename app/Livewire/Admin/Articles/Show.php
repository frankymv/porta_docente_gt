<?php

namespace App\Livewire\Admin\Articles;

use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Show extends Component
{
    public Article $article;

    public function mount(Article $article): void
    {
        $this->article = $article->load(['category', 'tags']);
    }

    public function togglePublish(): void
    {
        $this->article->update([
            'published_at' => $this->article->published_at ? null : now(),
        ]);

        $this->dispatch('notify', message: $this->article->published_at ? 'Contenido publicado' : 'Contenido pasado a borrador');
    }

    public function delete()
    {
        if ($this->article->cover_url && str_starts_with($this->article->cover_url, '/storage/')) {
            Storage::disk('public')->delete(substr($this->article->cover_url, strlen('/storage/')));
        }

        $this->article->delete();
        session()->flash('ok', 'Contenido eliminado');

        return $this->redirectRoute('admin.articles.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.shared.article-detail', [
            'backUrl' => route('admin.articles.index'),
            'editUrl' => route('admin.articles.edit', $this->article),
            'padded' => false,
        ])->title($this->article->title);
    }
}
