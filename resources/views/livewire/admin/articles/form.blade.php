<div class="mx-auto max-w-6xl">
    <a href="{{ route('admin.articles.index') }}" wire:navigate class="text-sm text-slate-500 hover:underline">← Volver a articulos</a>
    <h1 class="mt-2 text-2xl font-extrabold text-slate-900 dark:text-white">{{ $isEdit ? 'Editar articulo' : 'Nuevo articulo' }}</h1>

    <form wire:submit="save" class="mt-6 grid gap-6 lg:grid-cols-3">

        {{-- ============ Columna principal ============ --}}
        <div class="space-y-5 lg:col-span-2">
            <div class="card space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium" for="title">Título *</label>
                    <input id="title" type="text" wire:model.live.debounce.400ms="title" class="input" maxlength="200">
                    @error('title') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div x-data="{ edit: false }">
                    <label class="mb-1 block text-sm font-medium">Enlace (slug)</label>
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <span>/articulos/</span>
                        <template x-if="!edit"><span class="font-medium text-slate-700 dark:text-slate-200">{{ $slug ?: '…' }}</span></template>
                        <input x-show="edit" x-cloak type="text" wire:model="slug" class="input !py-1.5">
                        <button type="button" @click="edit = !edit" class="text-xs font-semibold text-brand-600" x-text="edit ? 'Listo' : 'Editar'"></button>
                    </div>
                    @error('slug') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium" for="excerpt">Resumen * <span class="font-normal text-slate-400">({{ mb_strlen($excerpt) }}/600)</span></label>
                    <textarea id="excerpt" wire:model.live.debounce.300ms="excerpt" rows="3" class="input" maxlength="600"></textarea>
                    @error('excerpt') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Editor Markdown --}}
            <div class="card">
                <div class="mb-3 flex items-center justify-between">
                    <label class="text-sm font-medium" for="body">Articulo (Markdown)</label>
                    <div class="inline-flex rounded-lg bg-slate-100 p-0.5 text-xs font-semibold dark:bg-slate-800">
                        <button type="button" wire:click="$set('preview', false)" class="rounded-md px-3 py-1 {{ ! $preview ? 'bg-white shadow dark:bg-slate-700' : 'text-slate-500' }}">Escribir</button>
                        <button type="button" wire:click="$set('preview', true)" class="rounded-md px-3 py-1 {{ $preview ? 'bg-white shadow dark:bg-slate-700' : 'text-slate-500' }}">Vista previa</button>
                    </div>
                </div>

                @if ($preview)
                    <div class="prose-article min-h-[16rem] rounded-xl border border-dashed border-slate-300 p-4 dark:border-slate-700">
                        {!! Str::markdown($body ?: '*Sin articulo todavía*', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
                    </div>
                @else
                    {{-- Atajos de formato con Alpine --}}
                    <div x-data="{
                            wrap(before, after = before) {
                                const el = $refs.editor;
                                const s = el.selectionStart, e = el.selectionEnd;
                                const sel = el.value.substring(s, e) || 'texto';
                                el.setRangeText(before + sel + after, s, e, 'select');
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                                el.focus();
                            },
                            line(prefix) {
                                const el = $refs.editor;
                                const s = el.selectionStart;
                                const start = el.value.lastIndexOf('\n', s - 1) + 1;
                                el.setRangeText(prefix, start, start, 'end');
                                el.dispatchEvent(new Event('input', { bubbles: true }));
                                el.focus();
                            }
                         }">
                        <div class="mb-2 flex flex-wrap gap-1 text-sm">
                            <button type="button" @click="wrap('**')" class="btn-ghost !px-2.5 !py-1 font-bold">B</button>
                            <button type="button" @click="wrap('*')" class="btn-ghost !px-2.5 !py-1 italic">I</button>
                            <button type="button" @click="line('## ')" class="btn-ghost !px-2.5 !py-1">H2</button>
                            <button type="button" @click="line('### ')" class="btn-ghost !px-2.5 !py-1">H3</button>
                            <button type="button" @click="line('- ')" class="btn-ghost !px-2.5 !py-1">• Lista</button>
                            <button type="button" @click="line('1. ')" class="btn-ghost !px-2.5 !py-1">1. Lista</button>
                            <button type="button" @click="line('> ')" class="btn-ghost !px-2.5 !py-1">❝</button>
                            <button type="button" @click="wrap('[', '](https://)')" class="btn-ghost !px-2.5 !py-1">Enlace</button>
                        </div>
                        <textarea id="body" x-ref="editor" wire:model.blur="body" rows="18" class="input font-mono"></textarea>
                    </div>
                @endif
                @error('body') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ============ Columna lateral ============ --}}
        <div class="space-y-5">
            {{-- Publicación --}}
            <div class="card space-y-4">
                <h2 class="font-bold">Publicación</h2>

                <div class="space-y-2 text-sm">
                    @foreach (['draft' => 'Borrador', 'published' => 'Publicado', 'scheduled' => 'Programar'] as $val => $label)
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="radio" wire:model.live="status" value="{{ $val }}" class="text-brand-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                @if ($status === 'scheduled')
                    <div>
                        <label class="mb-1 block text-sm font-medium">Fecha y hora</label>
                        <input type="datetime-local" wire:model="publish_at" class="input">
                        @error('publish_at') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>
                @endif

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="is_featured" class="rounded text-brand-500">
                    Articulo destacado
                </label>

                <button class="btn-primary w-full" wire:loading.attr="disabled" wire:target="save,cover">
                    <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Guardar cambios' : 'Crear articulo' }}</span>
                    <span wire:loading wire:target="save">Guardando…</span>
                </button>
            </div>

            {{-- Clasificación --}}
            <div class="card space-y-4">
                <h2 class="font-bold">Clasificación</h2>
                <div>
                    <label class="mb-1 block text-sm font-medium">Tipo *</label>
                    <select wire:model="type" class="input">
                        @foreach (\App\Models\Article::TYPES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Categoría *</label>
                    <select wire:model="category_id" class="input">
                        <option value="">Selecciona…</option>
                        @foreach ($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    @if ($categories->isEmpty())
                        <p class="mt-1 text-xs text-amber-600">Primero crea una categoría en «Categorías y etiquetas».</p>
                    @endif
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Etiquetas</label>
                    <div class="max-h-40 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2 dark:border-slate-700">
                        @forelse ($tags as $t)
                            <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-1 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                                <input type="checkbox" wire:model="selectedTags" value="{{ $t->id }}" class="rounded text-brand-500">
                                {{ $t->name }}
                            </label>
                        @empty
                            <p class="px-2 py-1 text-xs text-slate-500">Aún no hay etiquetas.</p>
                        @endforelse
                    </div>
                    <input type="text" wire:model="newTags" placeholder="Nuevas, separadas por coma" class="input mt-2">
                    @error('selectedTags.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Detalles --}}
            <div class="card space-y-4">
                <h2 class="font-bold">Detalles</h2>
                <div>
                    <label class="mb-1 block text-sm font-medium">Autor</label>
                    <input type="text" wire:model="author_name" class="input">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Enlace externo <span class="font-normal text-slate-400">(video, PDF, audio)</span></label>
                    <input type="url" wire:model="external_url" placeholder="https://…" class="input">
                    @error('external_url') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium">Duración (minutos)</label>
                    <input type="number" min="1" wire:model="duration_minutes" class="input">
                    <p class="mt-1 text-xs text-slate-500">Si lo dejas vacío, se calcula por la longitud del texto.</p>
                    @error('duration_minutes') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Portada --}}
            <div class="card space-y-3">
                <h2 class="font-bold">Portada</h2>

                @if ($cover)
                    <img src="{{ $cover->temporaryUrl() }}" alt="Vista previa" class="h-40 w-full rounded-xl object-cover">
                @elseif ($current_cover)
                    <img src="{{ $current_cover }}" alt="Portada actual" class="h-40 w-full rounded-xl object-cover">
                @endif

                <input type="file" wire:model="cover" accept="image/*"
                       class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:font-semibold file:text-brand-700">
                <div wire:loading wire:target="cover" class="text-xs text-slate-500">Subiendo…</div>
                @error('cover') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror

                @if ($cover || $current_cover)
                    <button type="button" wire:click="removeCover" class="text-sm font-semibold text-rose-600 hover:underline">Quitar portada</button>
                @endif
            </div>
        </div>
    </form>
</div>
