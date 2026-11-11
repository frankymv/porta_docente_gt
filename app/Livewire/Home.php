<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Inicio')]
class Home extends Component
{
    public string $search = '';

    public function goSearch()
    {
        return $this->redirect(route('articles.index', ['q' => $this->search]), navigate: true);
    }

    public function render()
    {
        $latest = Article::published()
            ->with('category')
            ->latest('published_at')
            ->take(24)
            ->get()
            ->groupBy('type');

        return view('livewire.home', [
            'stats' => [
                'articles' => Article::published()->where('type', 'articulo')->count(),
                'documents' => Article::published()->where('type', 'documento')->count(),
                'webinars' => Article::published()->where('type', 'webinar')->count(),
                'asistencia' => Article::published()->where('type', 'asistencia')->count(),
                'members' => User::count(),
            ],
            'latest' => $latest,
            'mostSaved' => Article::published()->withCount('savedBy')
                ->orderByDesc('saved_by_count')->orderByDesc('views')->take(5)->get(),
            'recentPosts' => Post::with('user')->withCount(['comments', 'likes'])
                ->latest()->take(3)->get(),
        ]);
    }
}
