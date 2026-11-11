<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('articulo')->index(); // articulo | documento | webinar | podcast
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author_name')->nullable();
            $table->text('excerpt');
            $table->longText('body')->nullable();          // Markdown
            $table->string('cover_url')->nullable();
            $table->string('external_url')->nullable();    // video, PDF, audio...
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('article_tag', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['article_id', 'tag_id']);
        });

        // Favoritos con nota personal ("Mi Espacio")
        Schema::create('article_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_user');
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
    }
};
