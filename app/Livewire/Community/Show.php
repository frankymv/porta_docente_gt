<?php

namespace App\Livewire\Community;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Post $post;

    #[Validate('required|string|min:2|max:2000')]
    public string $body = '';

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function addComment(): void
    {
        abort_unless(Auth::check(), 403);
        $this->validate();

        $this->post->comments()->create([
            'user_id' => Auth::id(),
            'body' => $this->body,
        ]);

        $this->reset('body');
        $this->dispatch('notify', message: 'Comentario publicado');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = $this->post->comments()->findOrFail($commentId);
        abort_unless(Auth::id() === $comment->user_id || Auth::id() === $this->post->user_id, 403);
        $comment->delete();
    }

    public function toggleLike(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $this->post->likes()->toggle(Auth::id());
    }

    public function render()
    {
        $this->post->load('user')->loadCount('likes');

        return view('livewire.community.show', [
            'comments' => $this->post->comments()->with('user')->get(),
            'liked' => Auth::check() && $this->post->likes()->where('users.id', Auth::id())->exists(),
        ])->title($this->post->title);
    }
}
