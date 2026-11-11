<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Gestión de usuarios (solo administradores: ver middleware en routes/web.php).
 */
#[Layout('layouts.admin')]
#[Title('Usuarios')]
class Users extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    /** '' = todos | 'admin' = administradores | clave de User::ROLES */
    #[Url(except: '')]
    public string $role = '';

    /** Usuario abierto en el panel de detalle. */
    public ?int $selectedId = null;

    public string $password = '';

    public string $password_confirmation = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'role'], true)) {
            $this->resetPage();
        }
    }

    public function select(int $id): void
    {
        $this->selectedId = User::findOrFail($id)->id;
        $this->resetPasswordForm();
    }

    public function close(): void
    {
        $this->selectedId = null;
        $this->resetPasswordForm();
    }

    private function resetPasswordForm(): void
    {
        $this->reset(['password', 'password_confirmation']);
        $this->resetValidation();
    }

    public function changePassword(): void
    {
        abort_unless($this->selectedId, 404);

        $this->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Escribe la nueva contraseña.',
            'password.confirmed' => 'La confirmación no coincide.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = User::findOrFail($this->selectedId);

        // 'hashed' en el cast de User se encarga del hash; se rota el remember_token
        // para cerrar las sesiones "recordarme" que el usuario tuviera abiertas.
        $user->forceFill([
            'password' => $this->password,
            'remember_token' => Str::random(60),
        ])->save();

        $this->resetPasswordForm();
        $this->dispatch('notify', message: "Contraseña de {$user->name} actualizada");
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            $this->dispatch('notify', message: 'No puedes eliminar tu propia cuenta.', type: 'error');

            return;
        }

        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            $this->dispatch('notify', message: 'No puedes eliminar al único administrador.', type: 'error');

            return;
        }

        // Sus publicaciones y comentarios de la comunidad se eliminan (cascade);
        // sus contenidos (artículos) se conservan, sin autor asignado (nullOnDelete).
        $name = $user->name;
        $user->delete();

        if ($this->selectedId === $id) {
            $this->close();
        }

        $this->dispatch('notify', message: "Usuario {$name} eliminado");
    }

    public function render()
    {
        $term = trim($this->search);

        $users = User::query()
            ->withCount(['articles', 'posts', 'comments'])
            ->when($this->role === 'admin', fn ($q) => $q->where('is_admin', true))
            ->when($this->role !== '' && $this->role !== 'admin', fn ($q) => $q->where('role', $this->role))
            ->when($term !== '', fn ($q) => $q->where(function ($w) use ($term) {
                $w->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('institution', 'like', "%{$term}%");
            }))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users', [
            'users' => $users,
            'selected' => $this->selectedId
                ? User::withCount(['articles', 'posts', 'comments'])->find($this->selectedId)
                : null,
        ]);
    }
}
