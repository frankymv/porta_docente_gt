<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('docente')->after('email'); // docente | directivo | estudiante | admin
            $table->string('institution')->nullable()->after('role');
            $table->string('country', 60)->nullable()->after('institution');
            $table->string('subjects')->nullable()->after('country'); // "Matemáticas, Ciencias"
            $table->text('bio')->nullable()->after('subjects');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'institution', 'country', 'subjects', 'bio']);
        });
    }
};
