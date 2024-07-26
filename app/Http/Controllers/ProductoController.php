<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Producto;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {
            $productos = Producto::with('marca','categoria')->get();
            return ApiResponse::success('Lista de productos', 200, $productos);
        } catch (Exception $th) {
            return ApiResponse::error('Error al obtener la lista de Productos: '.$th->getMessage(), 500);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'nombre' => 'required|unique:productos',
                'precio' => 'required|numeric|between:0,9999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
            ]);

            $producto = Producto::create($request->all());
            return ApiResponse::success('Producto creado exitosamente', 201, $producto); // 201 Created

        } catch (ValidationException $th) {
            $errors = $th->validator->errors()->toArray(); // Get the validation errors
            if(isset($errors['categoria_id'])){
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }
            if(isset($errors['marca_id'])){
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);
            }
            return ApiResponse::error('Errores de validación', 422, $errors);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Producto::with('marca', 'categoria')->findOrFail($id);
            return ApiResponse::success('Producto obtenido', 200, $product); // 200 OK
        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Producto no encontrada', 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $product = Producto::findOrFail($id);
            $request->validate([
                'nombre' => 'required|unique:productos,nombre,'.$product->id,
                'precio' => 'required|numeric|between:0,9999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id' => 'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id',
            ]);
            $product->update($request->all());
            return ApiResponse::success('Producto actualizada exitosamente', 200 , $product);

        } catch (ValidationException $th) {
            $errors = $th->validator->errors()->toArray(); // Get the validation errors

            if(isset($errors['categoria_id'])){
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }
            if(isset($errors['marca_id'])){
                $errors['marca'] = $errors['marca_id'];
                unset($errors['marca_id']);
            }

            return ApiResponse::error('Errores de validación', 422, $errors);

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
            $product = Producto::findOrFail($id);
            $product->delete();
            return ApiResponse::success('Producto eliminado exitosamente',200);

        } catch (ModelNotFoundException $th) {
            return ApiResponse::error('Product no encontrada', 404);
        }

    }
}
