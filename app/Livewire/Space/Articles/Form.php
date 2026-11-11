<?php

namespace App\Livewire\Space\Articles;

use App\Livewire\Concerns\HandlesArticleForm;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Crear/editar contenidos propios (cualquier usuario autenticado).
 */
#[Layout('layouts.app')]
class Form extends Component
{
    use HandlesArticleForm;

    protected function indexRoute(): string
    {
        return 'space.articles.index';
    }

    protected function isAdminContext(): bool
    {
        return false;
    }

    protected function authorizeArticle(Article $article): void
    {
        abort_unless($article->user_id === Auth::id(), 403, 'Solo puedes editar tus propios contenidos.');
    }
}
