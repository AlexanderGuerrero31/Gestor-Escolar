<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla pivote muchos-a-muchos: un alumno puede tener varios tutores
     * y un tutor puede tener varios alumnos a su cargo.
     */
    public function up(): void
    {
        Schema::create('alumno_tutor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('tutores')->cascadeOnDelete();
            $table->string('parentesco', 50)->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'tutor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumno_tutor');
    }
};
