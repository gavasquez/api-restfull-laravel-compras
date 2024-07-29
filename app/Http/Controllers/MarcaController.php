<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Marca;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

class MarcaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $marcas = Marca::all();
            return ApiResponse::success("Lista de Marcas", 200, $marcas);

        } catch (Exception $th) {
            return ApiResponse::error('Error al obtener la lista de Marcas: '.$th->getMessage(), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required|unique:marcas'
            ]);

            $marca = Marca::create($request->all());
            return ApiResponse::success('Marca creada exitosamente', 201 , $marca);

        } catch (ValidationException $th) {
            return ApiResponse::error('Error de validación: '.$th->getMessage(), 422);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $marca = Marca::findOrFail($id); // Busca un registro en la base de datos
            return ApiResponse::success('Marca obtenida exitosamente', 200 , $marca);
        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Marca no encontrada', 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $marca = Marca::findOrFail($id);
            $request->validate([
                'nombre' => [
                    'required',
                    Rule::unique('marcas')->ignore($marca) // Ignora el nombre de la categoria actual
                ]
            ]);
            $marca->update($request->all()); // Actualiza el registro en la base de datos
            return ApiResponse::success('Marca actualizada exitosamente', 200 , $marca);

        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Marca no encontrada', 404);
        } catch(Exception $th){
            return ApiResponse::error('Error: ' .$th->getMessage(), 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $marca = Marca::findOrFail($id);
            $marca->delete();
            return ApiResponse::success('Marca eliminada exitosamente',200);

        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Marca no encontrada', 404);
        }

    }

    public function productosPorMarca($id){
        try {
            $marca = Marca::with('productos')->findOrFail($id);
            return ApiResponse::success('Marcas por Producto',200,$marca);
        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Marca no encontrada', 404);
        }
    }
}
