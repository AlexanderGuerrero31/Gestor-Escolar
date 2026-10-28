<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * La asistencia ahora se registra por materia. Las asistencias anteriores
     * quedan sin materia ("Sin materia") porque antes no se guardaba ese dato.
     */
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreignId('materia_id')->nullable()->constrained('materias')->cascadeOnDelete();
        });

        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropUnique(['alumno_id', 'fecha']);
        });

        Schema::table('asistencias', function (Blueprint $table) {
            $table->unique(['alumno_id', 'materia_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropUnique(['alumno_id', 'materia_id', 'fecha']);
        });

        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('materia_id');
        });

        Schema::table('asistencias', function (Blueprint $table) {
            $table->unique(['alumno_id', 'fecha']);
        });
    }
};
