<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    public const KINDS = [
        'pregunta' => 'Pregunta',
        'recurso' => 'Recurso',
        'experiencia' => 'Experiencia',
    ];

    protected $fillable = ['user_id', 'kind', 'title', 'body'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function getKindLabelAttribute(): string
    {
        return self::KINDS[$this->kind] ?? ucfirst($this->kind);
    }
}
