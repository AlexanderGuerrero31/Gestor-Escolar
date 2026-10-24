<?php

namespace App\Http\Controllers;

use App\Models\Grado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradoController extends Controller
{
    public function index(): View
    {
        return view('grados.index', [
            'grados' => Grado::orderBy('nombre')->get(),
            'grado' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Grado::create($this->validarDatos($request));

        return redirect()->route('grados.index')->with('exito', 'Grado registrado correctamente.');
    }

    public function edit(Grado $grado): View
    {
        return view('grados.index', [
            'grados' => Grado::orderBy('nombre')->get(),
            'grado' => $grado,
        ]);
    }

    public function update(Request $request, Grado $grado): RedirectResponse
    {
        $grado->update($this->validarDatos($request, $grado));

        return redirect()->route('grados.index')->with('exito', 'Grado actualizado correctamente.');
    }

    public function destroy(Grado $grado): RedirectResponse
    {
        if ($grado->grupos()->exists()) {
            return redirect()->route('grados.index')
                ->with('error', 'No se puede eliminar el grado porque tiene grupos asociados.');
        }

        $grado->delete();

        return redirect()->route('grados.index')->with('exito', 'Grado eliminado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Grado $grado = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('grados', 'nombre')->ignore($grado)],
        ], [
            'nombre.required' => 'El nombre del grado es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'nombre.unique' => 'Ya existe un grado con ese nombre.',
        ]);
    }
}
