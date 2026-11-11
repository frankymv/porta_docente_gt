<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'docente' => 'Docente',
        //'directivo' => 'Directivo',
        'estudiante' => 'Estudiante',
        'administrador' => 'Administrador',
        //'formador' => 'Formador de maestros',
    ];

        public const ROLESUSER = [
        'docente' => 'Docente',
        //'directivo' => 'Directivo',
        'estudiante' => 'Estudiante',
        //'administrador' => 'Administrador',
        //'formador' => 'Formador de maestros',
    ];

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'institution', 'country', 'subjects', 'bio',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /** Contenidos (artículos, documentos…) creados por este usuario. */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    /** Artículos guardados (favoritos) con su nota personal. */
    public function savedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_user')
            ->withPivot('note')
            ->withTimestamps();
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }
}
