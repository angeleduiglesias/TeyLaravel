<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\IndexClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Http\Requests\Cliente\DestroyClienteRequest;
use Illuminate\Http\JsonResponse;
use App\Services\FirebaseAuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Cliente;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexClienteRequest $request): JsonResponse
    {
        $clientes = Cliente::all();

        return response()->json($clientes, 200); // Código de respuesta HTTP 200 OK
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request, FirebaseAuthService $firebaseAuth)
    {
        // Datos validados
        $data = $request->validated();
        
        // Genera o asigna la contraseña predeterminada.
        $passwordTemporal = 'DitechPeru2025';

        // Crea el Cliente en Firebase con la contraseña temporal.
        try {
            $firebaseUser = $firebaseAuth->createUser($data['email'], $passwordTemporal);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        // Crea el Cliente en la base de datos local.
        $user = User::create([
            'full_name'     => $data['full_name'],
            'email'    => $data['email'],
            'password' => Hash::make($passwordTemporal), // Guarda la contraseña encriptada
            'rol'      => 'cliente',
        ]);

        // Crea el los datos adicionales del Cliente en la tabla cliente.
        $cliente = Cliente::create([
            'dni'      => $data['dni'],
            'full_name' => $data['full_name'],
            'telefono'  => $data['telefono'],
            'user_id'   => $user->id,
        ]);

        // Si no se puede enviar el correo, se puede enviar un correo manualmente.
        try {
            $firebaseAuth->sendPasswordResetEmail($user['email']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Cliente creado, pero no se pudo enviar el correo de restablecimiento.'
            ], 422);
        }

        return response()->json([
            'message' => 'Cliente registrado correctamente. Se ha enviado un correo para restablecer la contraseña.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowClienteRequest $request, Cliente $cliente): JsonResponse
    {
        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(updateClienteRequest $request, string $id)
    {
        
        $cliente = Cliente::find($id);
        $data = $request->validated();

        // Se actualiza el registro del cliente.
        $cliente->update($data);

        return response()->json([
            'message' => 'Cliente actualizado con éxito.',
            'cliente' => $cliente
        ], 200);
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyClienteRequest $request, string $id): JsonResponse
    {
        //Buscamos al cliente por su ID
        $cliente = Cliente::find($id);  

        // Si no se encuentra el cliente, se retorna un error.
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado.'], 404);
        }

        // Elimina el cliente de Firebase y de la tabla Clientes.
        try {
            $firebaseAuth->deleteUser($cliente->user->email);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        // Elimina el cliente de la base de datos local.
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado con éxito.'
        ], 200);
    }
}
