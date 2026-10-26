<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Aula;
use App\Models\Calificacion;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Tutor;
use Illuminate\Database\Seeder;

/**
 * Datos de prueba del gestor escolar. Se puede ejecutar varias veces
 * (php artisan db:seed) sin duplicar registros.
 */
class EscuelaSeeder extends Seeder
{
    public function run(): void
    {
        $grados = collect(['Primero', 'Segundo', 'Tercero'])
            ->mapWithKeys(fn (string $nombre) => [$nombre => Grado::firstOrCreate(['nombre' => $nombre])]);

        $ciclo = CicloEscolar::updateOrCreate(
            ['nombre' => '2026-2027'],
            ['fecha_inicio' => '2026-08-24', 'fecha_fin' => '2027-07-09', 'activo' => true],
        );

        $aulas = collect([
            ['Aula 101', 30], ['Aula 102', 30], ['Aula 103', 25], ['Laboratorio', 20],
        ])->mapWithKeys(fn (array $a) => [$a[0] => Aula::updateOrCreate(['nombre' => $a[0]], ['capacidad' => $a[1]])]);

        $materias = collect([
            ['MAT-01', 'Matemáticas', 5], ['ESP-01', 'Español', 5], ['CIE-01', 'Ciencias', 4],
            ['HIS-01', 'Historia', 3], ['ING-01', 'Inglés', 3], ['EDF-01', 'Educación Física', 2],
        ])->mapWithKeys(fn (array $m) => [
            $m[1] => Materia::updateOrCreate(['clave' => $m[0]], ['nombre' => $m[1], 'horas_semana' => $m[2]]),
        ]);

        // Docentes y las materias que imparten (muchos-a-muchos: docente_materia).
        $docentes = collect([
            ['Laura', 'Hernández', 'Ríos', 'laura.hernandez@escuela.mx', ['Matemáticas', 'Ciencias']],
            ['Carlos', 'Méndez', 'Soto', 'carlos.mendez@escuela.mx', ['Español', 'Historia']],
            ['Ana', 'Torres', 'Vega', 'ana.torres@escuela.mx', ['Ciencias']],
            ['Miguel', 'Ramírez', 'Luna', 'miguel.ramirez@escuela.mx', ['Inglés']],
            ['Sofía', 'Castillo', 'Paz', 'sofia.castillo@escuela.mx', ['Educación Física', 'Historia']],
        ])->mapWithKeys(function (array $d) use ($materias) {
            $docente = Docente::updateOrCreate(['correo' => $d[3]], [
                'nombre' => $d[0], 'apellido_paterno' => $d[1], 'apellido_materno' => $d[2],
                'telefono' => '55'.random_int(10000000, 99999999), 'especialidad' => $d[4][0],
            ]);
            $docente->materias()->syncWithoutDetaching(collect($d[4])->map(fn ($n) => $materias[$n]->id)->all());

            return [$d[0] => $docente];
        });

        // Tutores (6) y alumnos (8). Un tutor tiene 2 hijos y un alumno tiene 2 tutores.
        $tutores = collect([
            ['Roberto', 'Guzmán', 'Cruz', '5511111101'],
            ['María', 'López', 'Díaz', '5511111102'],
            ['José', 'Martínez', 'Ortiz', '5511111103'],
            ['Elena', 'Flores', 'Reyes', '5511111104'],
            ['Pedro', 'Sánchez', 'Mora', '5511111105'],
            ['Lucía', 'Vargas', 'Peña', '5511111106'],
        ])->map(fn (array $t) => Tutor::updateOrCreate(['telefono' => $t[3]], [
            'nombre' => $t[0], 'apellido_paterno' => $t[1], 'apellido_materno' => $t[2],
        ]))->values();

        $datosAlumnos = [
            ['2026001', 'Diego', 'Guzmán', 'López', '2014-03-12', [[0, 'Padre'], [1, 'Madre']]],
            ['2026002', 'Valeria', 'Guzmán', 'López', '2015-07-25', [[0, 'Padre'], [1, 'Madre']]],
            ['2026003', 'Mateo', 'Martínez', 'Flores', '2014-11-02', [[2, 'Padre'], [3, 'Madre']]],
            ['2026004', 'Camila', 'Sánchez', 'Vargas', '2014-01-30', [[4, 'Padre']]],
            ['2026005', 'Santiago', 'Pérez', 'Ruiz', '2014-09-18', [[5, 'Tía']]],
            ['2026006', 'Regina', 'Ortega', 'Salas', '2014-05-07', [[1, 'Madrina']]],
            ['2026007', 'Emiliano', 'Navarro', 'Gil', '2013-12-21', [[3, 'Madre']]],
            ['2026008', 'Ximena', 'Aguilar', 'Rojas', '2013-04-09', [[5, 'Madre']]],
        ];

        $alumnos = collect($datosAlumnos)->map(function (array $a) use ($tutores) {
            $alumno = Alumno::updateOrCreate(['matricula' => $a[0]], [
                'nombre' => $a[1], 'apellido_paterno' => $a[2], 'apellido_materno' => $a[3],
                'fecha_nacimiento' => $a[4],
            ]);
            $alumno->tutores()->syncWithoutDetaching(
                collect($a[5])->mapWithKeys(fn ($t) => [$tutores[$t[0]]->id => ['parentesco' => $t[1]]])->all()
            );

            return $alumno;
        })->values();

        // Grupos e inscripciones (cada alumno en un solo grupo del ciclo).
        $primeroA = Grupo::firstOrCreate(
            ['grado_id' => $grados['Primero']->id, 'ciclo_escolar_id' => $ciclo->id, 'nombre' => 'A'],
            ['aula_id' => $aulas['Aula 101']->id],
        );
        $primeroB = Grupo::firstOrCreate(
            ['grado_id' => $grados['Primero']->id, 'ciclo_escolar_id' => $ciclo->id, 'nombre' => 'B'],
            ['aula_id' => $aulas['Aula 102']->id],
        );
        $segundoA = Grupo::firstOrCreate(
            ['grado_id' => $grados['Segundo']->id, 'ciclo_escolar_id' => $ciclo->id, 'nombre' => 'A'],
            ['aula_id' => $aulas['Aula 103']->id],
        );

        $asignacion = [0 => $primeroA, 1 => $primeroA, 2 => $primeroA, 3 => $primeroB, 4 => $primeroB, 5 => $primeroB, 6 => $segundoA, 7 => $segundoA];
        foreach ($asignacion as $indice => $grupo) {
            Inscripcion::firstOrCreate(
                ['alumno_id' => $alumnos[$indice]->id, 'grupo_id' => $grupo->id],
                ['fecha_inscripcion' => '2026-08-20'],
            );
        }

        // Horarios (sin empalmes de docente ni de grupo; cada docente imparte la materia).
        $horarios = [
            [$primeroA, 'Matemáticas', 'Laura', 'Lunes', '08:00', '09:00'],
            [$primeroA, 'Español', 'Carlos', 'Lunes', '09:00', '10:00'],
            [$primeroA, 'Ciencias', 'Ana', 'Martes', '08:00', '09:00'],
            [$primeroB, 'Español', 'Carlos', 'Lunes', '08:00', '09:00'],
            [$primeroB, 'Matemáticas', 'Laura', 'Lunes', '09:00', '10:00'],
            [$segundoA, 'Inglés', 'Miguel', 'Martes', '09:00', '10:00'],
            [$segundoA, 'Educación Física', 'Sofía', 'Miércoles', '08:00', '09:00'],
        ];
        foreach ($horarios as [$grupo, $materia, $docente, $dia, $inicio, $fin]) {
            Horario::firstOrCreate(
                ['grupo_id' => $grupo->id, 'dia_semana' => $dia, 'hora_inicio' => $inicio.':00'],
                ['materia_id' => $materias[$materia]->id, 'docente_id' => $docentes[$docente]->id, 'hora_fin' => $fin.':00'],
            );
        }

        // Calificaciones: Matemáticas y Español, periodos 1 y 2, valores de 5.0 a 10.0.
        $notas = [
            [9.5, 9.0, 8.5, 9.0], [8.0, 8.5, 7.5, 8.0], [6.5, 7.0, 7.0, 7.5], [9.0, 9.5, 9.0, 10.0],
            [5.0, 5.5, 6.0, 6.5], [7.5, 8.0, 8.0, 8.5], [8.5, 8.0, 9.0, 9.0], [10.0, 9.5, 9.5, 10.0],
        ];
        foreach ($alumnos as $i => $alumno) {
            $combinaciones = [['Matemáticas', 1], ['Matemáticas', 2], ['Español', 1], ['Español', 2]];
            foreach ($combinaciones as $j => [$materia, $periodo]) {
                Calificacion::updateOrCreate(
                    ['alumno_id' => $alumno->id, 'materia_id' => $materias[$materia]->id, 'periodo' => $periodo],
                    ['calificacion' => $notas[$i][$j]],
                );
            }
        }

        // Asistencias de una semana de clases (lunes a viernes).
        $estados = ['presente', 'presente', 'presente', 'retardo', 'presente', 'falta', 'justificada'];
        foreach ($alumnos as $i => $alumno) {
            foreach (range(0, 4) as $dia) {
                $estado = $estados[($i * 3 + $dia * 2) % count($estados)];
                Asistencia::updateOrCreate(
                    ['alumno_id' => $alumno->id, 'fecha' => date('Y-m-d', strtotime("2026-10-19 +{$dia} days"))],
                    [
                        'estado' => $estado,
                        'observaciones' => $estado === 'justificada' ? 'Presentó justificante médico' : null,
                    ],
                );
            }
        }
    }
}
