<?php

namespace App\Livewire\Concerns;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

/**
 * Lógica compartida del formulario de contenidos (crear/editar).
 * La usan el panel de administración y "Mi Espacio" (usuarios autores).
 */
trait HandlesArticleForm
{
    use WithFileUploads;

    public ?Article $article = null;

    public string $type = 'articulo';

    // Sin tipo estricto: los <select>/<input number> envían '' cuando están vacíos
    public $category_id = null;

    public string $title = '';

    public string $slug = '';

    public string $author_name = '';

    public string $excerpt = '';

    public string $body = '';

    public string $external_url = '';

    public $duration_minutes = null;

    public bool $is_featured = false;

    /** draft | published | scheduled */
    public string $status = 'published';

    public string $publish_at = '';

    public array $selectedTags = [];

    public string $newTags = '';

    public $cover = null;

    public ?string $current_cover = null;

    public bool $preview = false;

    /** Tamaño máximo de la portada en KB (1024 KB = 1 MB). Cambia solo este valor. */
    public const COVER_MAX_KB = 1024;

    /** Ruta (nombre) del listado al que se vuelve tras guardar. */
    abstract protected function indexRoute(): string;

    /** true solo en el panel de administración (rutas protegidas por middleware). */
    abstract protected function isAdminContext(): bool;

    /** Sobrescribir para restringir quién puede tocar un contenido. */
    protected function authorizeArticle(Article $article): void
    {
        //
    }

    public function mount(?Article $article = null): void
    {
        if ($article && $article->exists) {
            $this->authorizeArticle($article);

            $this->article = $article;
            $this->type = $article->type;
            $this->category_id = $article->category_id;
            $this->title = $article->title;
            $this->slug = $article->slug;
            $this->author_name = (string) $article->author_name;
            $this->excerpt = $article->excerpt;
            $this->body = (string) $article->body;
            $this->external_url = (string) $article->external_url;
            $this->duration_minutes = $article->duration_minutes;
            $this->is_featured = $article->is_featured;
            $this->current_cover = $article->cover_url;
            $this->selectedTags = $article->tags()->pluck('tags.id')->map(fn ($id) => (string) $id)->all();

            if (! $article->published_at) {
                $this->status = 'draft';
            } elseif ($article->published_at->isFuture()) {
                $this->status = 'scheduled';
                $this->publish_at = $article->published_at->format('Y-m-d\TH:i');
            } else {
                $this->status = 'published';
            }
        } else {
            $this->author_name = Auth::user()->name;
        }
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(array_keys(Article::TYPES))],
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'alpha_dash', 'max:220', Rule::unique('articles', 'slug')->ignore($this->article?->id)],
            'author_name' => ['nullable', 'string', 'max:120'],
            'excerpt' => ['required', 'string', 'max:600'],
            'body' => ['nullable', 'string', 'max:200000'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'is_featured' => ['boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled'])],
            'publish_at' => ['required_if:status,scheduled', 'nullable', 'date'],
            'selectedTags' => ['array'],
            'selectedTags.*' => ['exists:tags,id'],
            'newTags' => ['nullable', 'string', 'max:300'],
            'cover' => ['nullable', 'image', 'max:'.self::COVER_MAX_KB],
        ];
    }

    protected function messages(): array
    {
        return [
            'category_id.required' => 'Selecciona una categoría.',
            'publish_at.required_if' => 'Indica la fecha y hora de publicación.',
            'cover.uploaded' => 'No se pudo subir la imagen. Verifica que pese menos de 1 MB y que upload_max_filesize en php.ini lo permita.',
            'cover.image' => 'La portada debe ser una imagen (jpg, png, webp…).',
            'cover.max' => 'La portada no debe superar 1 MB.',
        ];
    }

    /**
     * Livewire arma el error "The cover failed to upload." con la traducción
     * `validation.uploaded`, no con messages(); aquí la ponemos en español.
     */
    protected function registerUploadMessages(): void
    {
        $lines = [
            'validation.uploaded' => 'No se pudo subir la imagen. Verifica que pese menos de '
                .(self::COVER_MAX_KB / 1024).' MB y que sea JPG, PNG o WebP.',
        ];

        foreach (array_unique([app()->getLocale(), app()->getFallbackLocale()]) as $locale) {
            app('translator')->addLines($lines, $locale);
        }
    }

    /** Se llama al elegir un archivo nuevo: borra el error del intento anterior. */
    public function resetCoverError(): void
    {
        $this->resetErrorBag('cover');
        $this->resetValidation('cover');
    }

    /** Hook de Livewire: justo antes de asignar una nueva subida a $cover. */
    public function updatingCover(): void
    {
        $this->resetCoverError();
    }

    public function removeCover(): void
    {
        $this->cover = null;
        $this->current_cover = null;
    }

    public function updatedTitle(string $value): void
    {
        // Sugiere el slug solo mientras se crea y no se ha editado a mano
        if (! $this->article) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        // Se vuelve a comprobar en cada guardado: nunca confiar solo en el mount
        if ($this->article) {
            $this->authorizeArticle($this->article);
        }

        $this->validate();

        $article = $this->article ?? new Article(['user_id' => Auth::id()]);

        $article->fill([
            'type' => $this->type,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => $this->uniqueSlug(Str::slug($this->slug ?: $this->title), $article->id),
            'author_name' => $this->author_name ?: null,
            'excerpt' => $this->excerpt,
            'body' => $this->body ?: null,
            'external_url' => $this->external_url ?: null,
            'duration_minutes' => $this->duration_minutes ?: null,
            'published_at' => $this->resolvePublishedAt($article),
            'cover_url' => $this->resolveCover($article),
        ]);

        // Destacar solo lo decide el administrador
        if ($this->isAdminContext()) {
            $article->is_featured = $this->is_featured;
        }

        $article->save();
        $article->tags()->sync($this->resolveTagIds());

        // Diagnóstico: revisa storage/logs/laravel.log si la portada no aparece
        logger()->info('Contenido guardado', [
            'id' => $article->id,
            'cover_recibida' => (bool) $this->cover,
            'cover_url' => $article->cover_url,
        ]);

        session()->flash('ok', $this->article ? 'Contenido actualizado' : 'Contenido creado');

        return $this->redirectRoute($this->indexRoute(), navigate: true);
    }

    private function resolvePublishedAt(Article $article): ?Carbon
    {
        return match ($this->status) {
            'draft' => null,
            'scheduled' => Carbon::parse($this->publish_at),
            default => ($article->published_at && $article->published_at->isPast())
                ? $article->published_at
                : now(),
        };
    }

    private function resolveCover(Article $article): ?string
    {
        $old = $article->cover_url;

        if ($this->cover) {
            $this->deleteStoredCover($old);

            // Ruta relativa (requiere `php artisan storage:link`); así funciona en cualquier dominio
            return '/storage/'.$this->cover->store('covers', 'public');
        }

        if ($this->current_cover === null) {
            $this->deleteStoredCover($old);

            return null;
        }

        return $old;
    }

    private function deleteStoredCover(?string $url): void
    {
        if ($url && str_starts_with($url, '/storage/')) {
            Storage::disk('public')->delete(substr($url, strlen('/storage/')));
        }
    }

    private function resolveTagIds(): array
    {
        $ids = collect($this->selectedTags)->map(fn ($id) => (int) $id);

        collect(explode(',', $this->newTags))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->unique()
            ->each(function ($name) use (&$ids) {
                $tag = Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
                $ids->push($tag->id);
            });

        return $ids->unique()->values()->all();
    }

    private function uniqueSlug(string $base, ?int $ignoreId): string
    {
        $slug = $base !== '' ? $base : 'contenido';
        $i = 2;

        while (Article::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function render()
    {
        return view('livewire.shared.article-form', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'isEdit' => (bool) $this->article,
            'backUrl' => route($this->indexRoute()),
            'coverMaxKb' => self::COVER_MAX_KB,
            'canFeature' => $this->isAdminContext(),
            'padded' => ! $this->isAdminContext(),
        ])->title($this->article ? 'Editar contenido' : 'Nuevo contenido');
    }
}
