<?php

namespace App\Http\Controllers\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Empresa;

class FillDocuments extends Controller
{
    public function MinutaDinerario(Request $request) {
        // Verificamos si el cliente existe
        $cliente = Auth::user();

        if (!$cliente) {
            return abort(404, 'Cliente no encontrado');
        }

        // Validamos el tipo de empresa
        $tipo_empresa = $empresa->tipo_empresa;

        if ($tipo_empresa == 'SAC') {
            $data = [
                'nombre_empresa' => $request->nombre_empresa,
                'actividades' => $request->actividades,
                'rubro' => $request->rubro,
                'tipo_empresa' => $tipo_empresa,
                'numero_socios' => $request->numero_socios,
                'tipo_aporte' => $request->tipo_aporte,
                'rango_capital' => $request->rango_capital
            ];

            return view('minuta_dinerario_sac', $data);
        } else {
            return response()->json([
                'error' => 'Faltan algunos datos',
                'message' => 'Por favor, complete todos los campos requeridos.'
            ], 400);
        }
    }
}
