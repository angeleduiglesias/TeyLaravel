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


class FormController extends Controller
{
    

    public function store(PreFormRequest $request, FirebaseAuthService $firebaseAuth)
    {
        try {
            $data = $request->validated();

            // Crea el usuario y cliente
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make('DitechPeru2025'),
                'rol' => 'cliente',
                'remember_token' => Str::random(60),
            ]);
            $user->save();


            $cliente = Cliente::create([
                'dni' => $data['dni'],
                'nombre' => $data['nombre'],
                'apellidos' => $data['apellidos'],
                'telefono' => $data['telefono'],
                'estado' => 'inactivo',
                'user_id' => $user->id,
                'remember_token' => Str::random(60),
                
            ]);
            $cliente->save();


            $empresa = Empresa::create([
                'tipo_aporte' => $data['tipo_aporte'],
                'rango_capital' => $data['rango_capital'],
                'rubro' => $data['rubro'],
                'actividades' => $data['actividades'],
                'tipo_empresa' => $data['tipo_empresa'],
                'nombre_empresa' => $data['nombre_empresa'],
                'posible_nombre1' => $data['posible_nombre1'],
                'posible_nombre2' => $data['posible_nombre2'],
                'posible_nombre3' => $data['posible_nombre3'],
                'posible_nombre4' => $data['posible_nombre4'],
                'numero_socios' => $data['numero_socios'],  //aca debes cambiar para que sea nulo
                'cliente_id' => $cliente->id,
            ]);
            $empresa->save();

            // Genera o asigna la contraseña predeterminada.
            $passwordTemporal = 'DitechPeru2025';
            // Crea el Cliente en Firebase con la contraseña temporal.
            try {
                $firebaseUser = $firebaseAuth->createUser($data['email'], $passwordTemporal);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            // Si no se puede enviar el correo mediante Firebase, se puede enviar un correo manualmente.
            try {
                $firebaseAuth->sendPasswordResetEmail($user['email']);
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
}
