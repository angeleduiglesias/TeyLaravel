<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\IndexClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Http\Requests\Cliente\DestroyClienteRequest;
use App\Http\Requests\Cliente\ShowClienteRequest;
use Illuminate\Http\JsonResponse;
use App\Services\FirebaseAuthService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Cliente;
use Illuminate\Support\Str;

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
     * Profile se utiliza para enviar los datos al panel identificando su id.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = auth()->user(); // Usuario autenticado (desde la tabla `users`)
        
        // Obtener cliente relacionado si usas relación con tabla clientes
        $cliente = Cliente::where('user_id', $user->id)->first();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
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
    public function destroy(DestroyClienteRequest $request, string $id, FirebaseAuthService $firebaseAuth): JsonResponse
    {
        //Buscamos al cliente por su ID
        $cliente = Cliente::find($id);  

        // Si no se encuentra el cliente, se retorna un error.
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado.'], 404);
        }

        // Elimina el cliente de Firebase y de la tabla Clientes.
        // try {
        //     $firebaseAuth->deleteUser($cliente->user->email);
        // } catch (\Exception $e) {
        //     return response()->json(['error' => $e->getMessage()], 422);
        // }

        // Elimina el cliente de la base de datos local.
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado con éxito.'
        ], 200);
    }
}
