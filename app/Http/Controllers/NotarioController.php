<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Notario\StoreNotarioRequest;
use App\Http\Requests\Notario\DestroyNotarioRequest;
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
        $documentos = Docuemento::all();
        $nombre_cliente = auth()->user()->cliente->nombre;
        $apellidos_cliente = auth()->user()->cliente->apellidos;
        return response()->json([
            'documentos' => $documentos,
            'nombre_cliente' => $nombre_cliente,
            'apellidos_cliente' => $apellidos_cliente
        ], 200); 
        
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
            'email'    => $data['email'],
            'password' => Hash::make($passwordTemporal), // Guarda la contraseña encriptada
            'rol'      => 'notario',
        ]);

        $notario = Notario::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
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
    public function show(ShowNotarioRequest $request, string $id): JsonResponse
    {
        // Buscamos el notario por su id
        $notario = Notario::find($id);

        // Si no se encuentra el notario, se devuelve un error 404
        if (!$notario) {
            return response()->json(['error' => 'Notario no encontrado'], 404);
        }

        // Devolvemos los datos del notario
        return response()->json($notario, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNotarioRequest $request, string $id)
    {
        $notario = Notario::find($id);
        $data = $request->validated();

        // Si no se encuentra el notario, se devuelve un error 404
        if (!$notario) {
            return response()->json(['error' => 'Notario no encontrado'], 404);
        }

        // Se actualiza el registro del notario y se notifica.
        $notario->update($data);
        return response()->json([
            'message' => 'Datos Actualizados con éxito.',
            'notario' => $notario
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyNotarioRequest $request, string $id, FirebaseAuthService $firebaseAuth): JsonResponse
    {
        //Buscamos el notario por su id
        $notario = Notario::find($id);

        // Si no se encuentra el notario, se devuelve un error 404
        if (!$notario) {
            return response()->json(['error' => 'Notario no encontrado'], 404);
        }

        // Eliminar el notario
        $notario->delete();

        // Eliminar el usuario asociado al notario
        $user = User::find($notario->user_id);
        if ($user) {
            $user->delete();
        }
        // Enviar una respuesta confirmando la eliminación.
        return response()->json(['message' => 'Notario eliminado correctamente'], 200);
    }
}
