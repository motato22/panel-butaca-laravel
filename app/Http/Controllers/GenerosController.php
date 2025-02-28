<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genero;

class GenerosController extends Controller
{
    // Editar género (actualizar nombre)
    public function update(Request $request, $id)
    {
        $request->validate(['nombre' => 'required|string|max:190']);

        $genero = Genero::findOrFail($id);
        $genero->nombre = $request->nombre;
        $genero->save();

        return response()->json(['success' => true, 'message' => 'Género actualizado correctamente.']);
    }

    // Eliminar género
    public function delete($id)
    {
        $genero = Genero::findOrFail($id);
        $genero->delete();

        return response()->json(['success' => true, 'message' => 'Género eliminado correctamente.']);
    }
}
