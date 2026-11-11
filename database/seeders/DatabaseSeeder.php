<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* --- Usuarios de ejemplo (contraseña: password) ---
        $docente = User::create([
            'name' => 'Laura Méndez',
            'email' => 'docente@example.com',
            'password' => 'password',
            'role' => 'docente',
            'institution' => 'Colegio Nuevo Horizonte',
            'country' => 'Guatemala',
            'subjects' => 'Matemáticas, Tecnología',
        ]);
        $directivo = User::create([
            'name' => 'Carlos Ruiz',
            'email' => 'directivo@example.com',
            'password' => 'password',
            'role' => 'directivo',
            'institution' => 'Instituto Central',
        ]);

        */

        // Administrador (accede a /admin)
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => 'Toto.2026',
            'role' => 'administrador',
        ]);
        $admin->forceFill(['is_admin' => true])->save();

        // --- Categorías y etiquetas ---
        $cats = collect(['Inteligencia Artificial', 'Pedagogía', 'Liderazgo', 'Didáctica', 'Educación y TIC'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n, 'slug' => Str::slug($n)])]);

        $tags = collect(['IA', 'evaluación', 'programación', 'gamificación', 'bienestar docente', 'competencias digitales'])
            ->mapWithKeys(fn ($n) => [$n => Tag::create(['name' => $n, 'slug' => Str::slug($n)])]);

        /*/ --- Contenidos de ejemplo (texto original de demostración) ---
        $items = [
            ['articulo', 'Inteligencia Artificial', 'Seguridad digital para educar en tiempos de IA', 'Claves para que estudiantes y docentes usen herramientas de IA con criterio, privacidad y responsabilidad.', ['IA generativa', 'competencias digitales']],
            //['articulo', 'Pedagogía', 'Cómo aprende la gente: ideas para el aula', 'Un recorrido por hallazgos sobre memoria, transferencia y evaluación formativa que cambian la forma de planear una clase.', ['evaluación', 'secundaria']],
            //['articulo', 'Liderazgo', 'El plan de mejoramiento institucional como herramienta pedagógica', 'Cómo convertir un documento de gestión en una hoja de ruta viva, medible y compartida por toda la comunidad.', ['evaluación']],
            //['articulo', 'Didáctica', 'Introducción al vibecoding para crear recursos educativos', 'Docentes sin experiencia técnica pueden prototipar recursos digitales conversando con asistentes de código.', ['IA generativa', 'programación']],
            //['articulo', 'Educación y TIC', 'Del libro a la pantalla: lectura en la cultura digital', 'Reflexión sobre qué cambia y qué permanece cuando leemos en entornos digitales.', ['competencias digitales']],
            //['articulo', 'Inteligencia Artificial', 'Cuando la IA piensa por nosotros: el riesgo de no razonar', 'Consecuencias pedagógicas de delegar tareas cognitivas en sistemas generativos y cómo diseñar actividades que lo eviten.', ['IA generativa']],
            //['articulo', 'Pedagogía', 'Bienestar docente como motor de calidad educativa', 'Experiencias que priorizan el cuidado de los maestros para mejorar los aprendizajes.', ['bienestar docente']],
            //['articulo', 'Didáctica', 'Primeros pasos para programar con niños de primaria', 'Secuencia sencilla de actividades desconectadas y con bloques para iniciar el pensamiento computacional.', ['programación', 'primaria']],
            ['documento', 'Pedagogía', 'Comprender lo que leemos: el lenguaje oral y la lectura', 'Resumen de 5 páginas sobre la relación entre comprensión oral y resultados lectores.', ['primaria']],
            //['documento', 'Inteligencia Artificial', '¿Es la IA el recurso definitivo para aprender idiomas?', 'Revisión breve de usos, límites y buenas prácticas de la IA en la enseñanza de lenguas.', ['IA generativa']],
            //['webinar', 'Inteligencia Artificial', 'El papel de la IA en la educación: ¿una maravillosa amenaza?', 'Conversación de 66 minutos sobre oportunidades y riesgos de la IA en la escuela.', ['IA generativa']],
            //['webinar', 'Didáctica', 'Diseñando experiencias de aprendizaje en la era de la IA', 'Sesión práctica para rediseñar actividades y evaluaciones.', ['gamificación', 'evaluación']],
            //['podcast', 'Liderazgo', 'Conversaciones con rectores: liderar el cambio', 'Directivos comparten decisiones difíciles y aprendizajes al transformar sus instituciones.', ['bienestar docente']],
        ];

        foreach ($items as $i => [$type, $cat, $title, $excerpt, $tagNames]) {
            $article = Article::create([
                'category_id' => $cats[$cat]->id,
                'user_id' => $directivo->id,
                'type' => $type,
                'title' => $title,
                'slug' => Str::slug($title).'-'.($i + 1),
                'author_name' => ['Equipo editorial', 'Dra. Ana Torres', 'Prof. Luis Herrera'][$i % 3],
                'excerpt' => $excerpt,
                'body' => $this->body($title),
                'duration_minutes' => in_array($type, ['webinar', 'podcast']) ? 45 + ($i * 3) : null,
                'external_url' => in_array($type, ['webinar', 'podcast']) ? 'https://example.com/recurso-'.($i + 1) : null,
                'views' => rand(20, 900),
                'is_featured' => $i < 3,
                'published_at' => now()->subDays($i * 3),
            ]);
            $article->tags()->sync(collect($tagNames)->map(fn ($n) => $tags[$n]->id));
        }

        // Guardados de ejemplo
        $docente->savedArticles()->attach(Article::first()->id, ['note' => 'Usar la lista de verificación con mi grupo de 9.º.']);
        $directivo->savedArticles()->attach(Article::first()->id);

        // --- Comunidad ---
        $p1 = Post::create([
            'user_id' => $docente->id,
            'kind' => 'pregunta',
            'title' => '¿Cómo evalúan trabajos hechos con ayuda de IA?',
            'body' => 'En mis clases de secundaria los estudiantes ya usan asistentes de IA. ¿Qué criterios o rúbricas están usando para evaluar el proceso y no solo el producto final?',
        ]);
        $p2 = Post::create([
            'user_id' => $directivo->id,
            'kind' => 'experiencia',
            'title' => 'Nuestro primer año con un plan de mejoramiento trimestral',
            'body' => 'Pasamos de un plan anual estático a ciclos de 10 semanas con metas pequeñas. Compartimos lo que funcionó y lo que no.',
        ]);
        Post::create([
            'user_id' => $docente->id,
            'kind' => 'recurso',
            'title' => 'Plantilla de rúbrica para proyectos de programación',
            'body' => 'Dejo una rúbrica de 4 criterios (planeación, lógica, creatividad y colaboración) que me ha funcionado en 5.º y 6.º.',
        ]);

        Comment::create(['post_id' => $p1->id, 'user_id' => $directivo->id, 'body' => 'Nosotros pedimos bitácoras de proceso y una breve sustentación oral. Cambió mucho la conversación.']);
        $p1->likes()->attach($directivo->id);
        $p2->likes()->attach($docente->id);

         */
    }



    private function body(string $title): string
    {
        return <<<MD
## Introducción

Este es un contenido de demostración para **{$title}**. Reemplázalo con tus propios textos desde un panel de administración o directamente en la base de datos. El cuerpo admite Markdown.

## Ideas clave

- Parte siempre de una necesidad real del aula.
- Prioriza actividades breves que puedas repetir y mejorar.
- Documenta lo que funciona y compártelo con tus colegas.

### Para llevar al aula

1. Elige un objetivo de aprendizaje concreto.
2. Diseña una actividad corta (15 a 20 minutos).
3. Recoge evidencia y ajusta.

> Enseñar bien es, en gran parte, observar con atención y ajustar a tiempo.

## Conclusión

Comparte tu experiencia en la comunidad y guarda este contenido en **Mi Espacio** para volver a él cuando lo necesites.
MD;
    }
}
