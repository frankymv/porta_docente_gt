<?php

namespace App\Livewire\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Comunidad')]
class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $filter = '';

    public string $kind = 'pregunta';

    public string $title = '';

    public string $body = '';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function publish(): void
    {
        abort_unless(Auth::check(), 403);

        $data = $this->validate([
            'kind' => ['required', Rule::in(array_keys(Post::KINDS))],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        Auth::user()->posts()->create($data);

        $this->reset(['title', 'body']);
        $this->resetPage();
        $this->dispatch('notify', message: 'Publicación creada');
    }

    public function toggleLike(int $postId): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        Post::findOrFail($postId)->likes()->toggle(Auth::id());
    }

    public function delete(int $postId): void
    {
        $post = Post::findOrFail($postId);
        abort_unless(Auth::id() === $post->user_id, 403);
        $post->delete();
        $this->dispatch('notify', message: 'Publicación eliminada');
    }

    public function render()
    {
        $posts = Post::with('user')
            ->withCount(['comments', 'likes'])
            ->when($this->filter, fn ($q) => $q->where('kind', $this->filter))
            ->latest()
            ->paginate(8);

        $liked = Auth::check()
            ? Auth::user()->likedPosts()->pluck('posts.id')->all()
            : [];

        return view('livewire.community.index', compact('posts', 'liked'));
    }
}
