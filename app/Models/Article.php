<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    public const TYPES = [
        'articulo' => 'Artículo',
        'documento' => 'Documento',
        'asistencia' => 'Asistencia Técnica',
        'webinar' => 'Webinar',
        //'podcast' => 'Podcast',
    ];

    protected $fillable = [
        'category_id', 'user_id', 'type', 'title', 'slug', 'author_name',
        'excerpt', 'body', 'cover_url', 'external_url', 'duration_minutes',
        'views', 'is_featured', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Autor (usuario que creó el contenido). */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'article_user')
            ->withPivot('note')
            ->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * URL que se usa en las vistas para mostrar la portada.
     * Las imágenes subidas ("/storage/covers/...") se sirven por la ruta `media`,
     * así NO dependen del enlace simbólico `public/storage`. Las URLs externas se dejan igual.
     */
    public function getCoverSrcAttribute(): ?string
    {
        return self::coverSrc($this->cover_url);
    }

    public static function coverSrc(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (str_starts_with($url, '/storage/')) {
            return route('media', ['path' => substr($url, strlen('/storage/'))]);
        }

        return $url;
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getReadingMinutesAttribute(): int
    {
        return $this->duration_minutes
            ?? max(1, (int) ceil(str_word_count(strip_tags((string) $this->body)) / 200));
    }
}
