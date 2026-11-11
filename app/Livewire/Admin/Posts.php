<?php

namespace App\Livewire\Admin;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Moderación de la comunidad')]
class Posts extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function deletePost(int $id): void
    {
        Post::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Publicación eliminada');
    }

    public function deleteComment(int $id): void
    {
        Comment::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Comentario eliminado');
    }

    public function render()
    {
        $term = trim($this->search);

        $posts = Post::with(['user', 'comments.user'])
            ->withCount('likes')
            ->when($term !== '', fn ($q) => $q->where(function ($w) use ($term) {
                $w->where('title', 'like', "%{$term}%")->orWhere('body', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.posts', compact('posts'));
    }
}
