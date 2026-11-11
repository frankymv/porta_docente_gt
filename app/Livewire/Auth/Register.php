<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Crear cuenta')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $role = 'docente';

    public string $institution = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'institution' => ['nullable', 'string', 'max:150'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function register()
    {
        $data = $this->validate();
        unset($data['password_confirmation']);

        $user = User::create($data);

        Auth::login($user);
        session()->regenerate();

        return $this->redirect(route('space'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
