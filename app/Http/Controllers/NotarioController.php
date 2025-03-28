<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Notario\StoreNotarioRequest;
use App\Services\FirebaseAuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class NotarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notarios = Notario::all();

        return response()->json($notarios, 200); // Código de respuesta HTTP 200 OK
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNotarioRequest $request, FirebaseAuthService $firebaseAuth)
    {
        // Datos validados
        $data = $request->validated();
        
        // Genera o asigna la contraseña predeterminada.
        $passwordTemporal = 'DitechPeru2025';

        // Crea el usuario en Firebase con la contraseña temporal.
        try {
            $firebaseUser = $firebaseAuth->createUser($data['email'], $passwordTemporal);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        // Crea el usuario en la base de datos local.
        $user = User::create([
            'full_name'     => $data['full_name'],
            'email'    => $data['email'],
            'password' => Hash::make($passwordTemporal), // Guarda la contraseña encriptada
            'rol'      => 'notario',
        ]);

        $notario = Notario::create([
            'full_name' => $data['full_name'],
            'telefono'  => $data['telefono'],
            'direccion' => $data['direccion'],
            'user_id'   => $user->id,
        ]);

        // Si no se puede enviar el correo, se puede enviar un correo manualmente.
        try {
            $firebaseAuth->sendPasswordResetEmail($user['email']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Usuario creado, pero no se pudo enviar el correo de restablecimiento.'
            ], 422);
        }

        return response()->json([
            'message' => 'Notario registrado correctamente. Se ha enviado un correo para restablecer la contraseña.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
