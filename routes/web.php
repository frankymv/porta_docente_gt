<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Livewire\Admin\Articles\Form as AdminArticlesForm;
use App\Livewire\Admin\Articles\Index as AdminArticlesIndex;
use App\Livewire\Admin\Articles\Show as AdminArticlesShow;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\Posts as AdminPosts;
use App\Livewire\Admin\Taxonomies as AdminTaxonomies;
use App\Livewire\Articles\Index as ArticlesIndex;
use App\Livewire\Articles\Show as ArticlesShow;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Community\Index as CommunityIndex;
use App\Livewire\Community\Show as CommunityShow;
use App\Livewire\Home;
use App\Livewire\Space\Articles\Form as SpaceArticlesForm;
use App\Livewire\Space\Articles\Index as SpaceArticlesIndex;
use App\Livewire\Space\Articles\Show as SpaceArticlesShow;
use App\Livewire\Space\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Imágenes subidas (portadas). Se sirven desde el disco "public" sin necesitar `storage:link`.
Route::get('/media/{path}', function (string $path) {
    abort_unless(str_starts_with($path, 'covers/') && ! str_contains($path, '..'), 404);
    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path, null, [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('media');

// Públicas
Route::get('/', Home::class)->name('home');

Route::get('/contenidos', ArticlesIndex::class)->name('articles.index');
Route::get('/contenidos/{article:slug}', ArticlesShow::class)->name('articles.show');

Route::get('/comunidad', CommunityIndex::class)->name('community.index');
Route::get('/comunidad/{post}', CommunityShow::class)->name('community.show');

// Invitados
Route::middleware('guest')->group(function () {
    Route::get('/cuenta/iniciar-sesion', Login::class)->name('login');
    Route::get('/cuenta/registro', Register::class)->name('register');
});

// Administración (solo usuarios con is_admin = true)
Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/contenidos', AdminArticlesIndex::class)->name('articles.index');
        Route::get('/contenidos/crear', AdminArticlesForm::class)->name('articles.create');
        Route::get('/contenidos/{article}/ver', AdminArticlesShow::class)->name('articles.show');
        Route::get('/contenidos/{article}/editar', AdminArticlesForm::class)->name('articles.edit');
        Route::get('/taxonomias', AdminTaxonomies::class)->name('taxonomies');
        Route::get('/comunidad', AdminPosts::class)->name('posts');
    });

// Autenticados
Route::middleware('auth')->group(function () {
    Route::get('/mi-espacio', Dashboard::class)->name('space');

    // Contenidos propios: cada usuario gestiona solo los que creó (se valida en cada componente)
    Route::get('/mi-espacio/contenidos', SpaceArticlesIndex::class)->name('space.articles.index');
    Route::get('/mi-espacio/contenidos/crear', SpaceArticlesForm::class)->name('space.articles.create');
    Route::get('/mi-espacio/contenidos/{article}/ver', SpaceArticlesShow::class)->name('space.articles.show');
    Route::get('/mi-espacio/contenidos/{article}/editar', SpaceArticlesForm::class)->name('space.articles.edit');

    Route::post('/cuenta/salir', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});
