<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cambia el "periodo" numérico de las calificaciones por una unidad de la
     * materia. Las calificaciones que ya existían se conservan: cada periodo N
     * se convierte en una unidad llamada "Unidad N" de su materia.
     */
    public function up(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->foreignId('unidad_id')->nullable()->constrained('unidades')->cascadeOnDelete();
        });

        foreach (DB::table('calificaciones')->select('id', 'materia_id', 'periodo')->get() as $fila) {
            $nombre = 'Unidad '.$fila->periodo;

            $unidadId = DB::table('unidades')
                ->where('materia_id', $fila->materia_id)
                ->where('nombre', $nombre)
                ->value('id');

            $unidadId ??= DB::table('unidades')->insertGetId([
                'materia_id' => $fila->materia_id,
                'nombre' => $nombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('calificaciones')->where('id', $fila->id)->update(['unidad_id' => $unidadId]);
        }

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropUnique(['alumno_id', 'materia_id', 'periodo']);
        });

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropColumn('periodo');
        });

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->unique(['alumno_id', 'unidad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropUnique(['alumno_id', 'unidad_id']);
        });

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->unsignedTinyInteger('periodo')->default(1);
        });

        // El periodo vuelve a ser la posición de la unidad dentro de su materia.
        $posiciones = [];
        foreach (DB::table('unidades')->orderBy('id')->get() as $unidad) {
            $posiciones[$unidad->materia_id] = ($posiciones[$unidad->materia_id] ?? 0) + 1;
            DB::table('calificaciones')->where('unidad_id', $unidad->id)->update(['periodo' => $posiciones[$unidad->materia_id]]);
        }

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unidad_id');
        });

        Schema::table('calificaciones', function (Blueprint $table) {
            $table->unique(['alumno_id', 'materia_id', 'periodo']);
        });
    }
};
