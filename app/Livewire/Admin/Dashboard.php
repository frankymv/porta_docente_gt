<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Resumen')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => [
                ['Contenidos publicados', Article::published()->count(), '📰'],
                ['Borradores y programados', Article::where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '>', now()))->count(), '📝'],
                ['Usuarios', User::count(), '👥'],
                ['Publicaciones de comunidad', Post::count(), '💬'],
            ],
            'latest' => Article::with('category')->latest('updated_at')->take(6)->get(),
            'popular' => Article::published()->orderByDesc('views')->take(5)->get(),
        ]);
    }
}
