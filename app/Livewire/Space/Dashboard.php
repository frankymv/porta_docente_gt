<?php

namespace App\Livewire\Space;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Mi Espacio')]
class Dashboard extends Component
{
    public string $name = '';

    public string $role = 'docente';

    public string $institution = '';

    public string $country = '';

    public string $subjects = '';

    public string $bio = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->role = $user->role;
        $this->institution = (string) $user->institution;
        $this->country = (string) $user->country;
        $this->subjects = (string) $user->subjects;
        $this->bio = (string) $user->bio;
    }

    public function saveProfile(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'institution' => ['nullable', 'string', 'max:150'],
            'country' => ['nullable', 'string', 'max:60'],
            'subjects' => ['nullable', 'string', 'max:200'],
            'bio' => ['nullable', 'string', 'max:600'],
        ]);

        Auth::user()->update($data);
        $this->dispatch('notify', message: 'Perfil actualizado');
    }

    public function unsave(int $articleId): void
    {
        Auth::user()->savedArticles()->detach($articleId);
        $this->dispatch('notify', message: 'Quitado de guardados');
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.space.dashboard', [
            'saved' => $user->savedArticles()->with('category')->latest('article_user.created_at')->get(),
            'posts' => $user->posts()->withCount(['comments', 'likes'])->latest()->get(),
        ]);
    }
}
