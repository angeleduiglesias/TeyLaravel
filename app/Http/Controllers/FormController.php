<?php

namespace App\Http\Controllers;

use App\Http\Requests\Form\PreFormRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\FirebaseAuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Empresa;
use App\Models\PosiblesNombres;
use App\Models\Pago;
use App\Requests\Form\RegistrarPago;
use App\Models\Tramite;
use App\Http\Requests\Form\PagosRequest;


class FormController extends Controller
{
    public function store(PreFormRequest $request, FirebaseAuthService $firebaseAuth)
    {
        try {
            $data = $request->validated();

            // Verificar si el email ya está registrado
            if (User::where('email', $data['email'])->exists()) {
                return response()->json(['error' => 'El correo electrónico ya está registrado.'], 422);
            }

            // Verificar si el DNI ya está registrado
            if (Cliente::where('dni', $data['dni'])->exists()) {
                return response()->json(['error' => 'El DNI ya está registrado.'], 422);
            }

            // Crea el usuario
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('DitechPeru2025'),
                'rol' => 'cliente',
                'remember_token' => Str::random(60),
            ]);

            // Crea el cliente
            $cliente = Cliente::create([
                'dni' => $data['dni'],
                'nombre' => $data['nombre'],
                'apellidos' => $data['apellidos'],
                'telefono' => $data['telefono'],
                'estado' => 'inactivo',
                'user_id' => $user->id,
                'remember_token' => Str::random(60),
            ]);

            // Crea la empresa
            $empresa = Empresa::create([
                'nombre_empresa' => $data['nombre_empresa'],
                'tipo_aporte' => $data['tipo_aporte'],
                'rango_capital' => $data['rango_capital'],
                'rubro' => $data['rubro'],
                'actividades' => $data['actividades'],
                'numero_socios' => $data['numero_socios'],
                'cliente_id' => $cliente->id,
            ]);

            // Crea los posibles nombres
            $posiblesNombres = PosiblesNombres::create([
                'posible_nombre1' => $data['posible_nombre1'],
                'posible_nombre2' => $data['posible_nombre2'],
                'posible_nombre3' => $data['posible_nombre3'],
                'posible_nombre4' => $data['posible_nombre4'],
                'empresa_id' => $empresa->id,
            ]);

            $tramite = Tramite::create([
                'estado' => 'pendiente',
                'cliente_id' => $cliente->id,
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDays(30),
            ]);

            // Crear usuario en Firebase
            try {
                $firebaseUser = $firebaseAuth->createUser($data['email'], 'DitechPeru2025');
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            // Enviar email de restablecimiento de contraseña
            try {
                $firebaseAuth->sendPasswordResetEmail($user->email);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Cliente creado, pero no se pudo enviar el correo de restablecimiento.'
                ], 422);
            }

            return response()->json(['message' => 'Formulario guardado correctamente.']);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }


    public function pagosPreform(PagosRequest $request)
    {
        $data = $request->validated(); 

        // Buscar cliente por DNI
        $cliente = Cliente::where('dni', $data['dni'])->first();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado.'], 404);
        }

        // Obtener el trámite pendiente más reciente
        $tramite = Tramite::where('cliente_id', $cliente->id)
            ->where('estado', 'pendiente')
            ->latest()
            ->first();

        if (!$tramite) {
            return response()->json(['error' => 'No se encontró un trámite pendiente para el cliente.'], 404);
        }

        // Registrar el pago
        $pago = Pago::create([
            'estado' => $data['estado'],
            'monto' => $data['monto'],
            'fecha' => now(),
            'comprobante' => $request->hasFile('comprobante') ? $request->file('comprobante')->store('pagos') : null,
            'tipo_pago' => $data['tipo_pago'],
            'tramite_id' => $tramite->id,
        ]);

        // Cambiar estado del trámite a "en_proceso"
        $tramite->estado = 'en_proceso';
        $tramite->save();

        return response()->json([
            'message' => 'Pago registrado correctamente y trámite actualizado a en_proceso.'
        ]);
    }


}
