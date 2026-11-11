<?php

namespace App\Livewire\Admin\Articles;

use App\Livewire\Concerns\HandlesArticleForm;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Crear/editar cualquier contenido (solo administradores: ver middleware en routes/web.php).
 */
#[Layout('layouts.admin')]
class Form extends Component
{
    use HandlesArticleForm;

    protected function indexRoute(): string
    {
        return 'admin.articles.index';
    }

    protected function isAdminContext(): bool
    {
        return true;
    }
}
