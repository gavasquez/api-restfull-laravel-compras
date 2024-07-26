<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Categoria;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categorias = Categoria::all();
            return ApiResponse::success("Lista de Categorias", 200, $categorias);
            //throw new Exception("Error al listar categorias");
        } catch (Exception $th) {
            return ApiResponse::error('Error al obtener la lista de categorías: '.$th->getMessage(), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required|unique:categorias'
            ]);

            $categoria = Categoria::create($request->all());
            return ApiResponse::success('Categoría creada exitosamente', 201 , $categoria);

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
            $categoria = Categoria::findOrFail($id); // Busca un registro en la base de datos
            return ApiResponse::success('Categoría obtenida exitosamente', 200 , $categoria);
        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Categoria no encontrada', 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $categoria = Categoria::findOrFail($id);
            $request->validate([
                'nombre' => [
                    'required',
                    Rule::unique('categorias')->ignore($categoria) // Ignora el nombre de la categoria actual
                ]
            ]);
            $categoria->update($request->all()); // Actualiza el registro en la base de datos
            return ApiResponse::success('Categoría actualizada exitosamente', 200 , $categoria);

        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Categoria no encontrada', 404);
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

            $categoria = Categoria::findOrFail($id);
            $categoria->delete();
            return ApiResponse::success('Categoria eliminada exitosamente',200);

        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Categoria no encontrada', 404);
        }

    }
}
