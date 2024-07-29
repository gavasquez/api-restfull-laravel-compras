<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Compra;
use App\Models\Producto;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Validator;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $productos = $request->input('productos'); // Array de productos

            // Validar los productos
            if(empty($productos)){
                return ApiResponse::error('No se proporcionaron productos', 400); // Error 400
            }

            // Validar la lista de productos
            $validator = Validator::make($request->all(), [
                'productos' => 'required|array',
                'productos.*.producto_id' => 'required|integer|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
            ]);

            if($validator->fails()){ // Si falla la validación
                return ApiResponse::error('Datos invalidos en la lista de productos', 400, $validator->errors()); // Error
            }

            // Validar productos duplicados
            $productosIds = array_column($productos, 'producto_id');  // Obtiene los ids de los productos
            if(count($productosIds) !== count(array_unique( $productosIds ))){ // Si hay productos duplicados
                return ApiResponse::error('No se permiten productos duplicados para la compra', 400);
            }

            $totalPagar = 0;
            $subTotal = 0;
            $compraItems = []; // Array para almacenar los items de la compra

            // Iteracion de los productos para calcular el total a pagar
            foreach($productos as $producto){
                $productoB = Producto::find($producto['producto_id']); // Busca el producto en la base de datos
                if(!$productoB){
                    return ApiResponse::error('Producto no encontrado', 404); // Error 404
                }

                // Validar la cantidad disponible de los productos
                if($productoB->cantidad_disponible < $producto['cantidad']){ // Si la cantidad disponible es menor a la cantidad solicitada
                    return ApiResponse::error('No hay suficiente stock para el producto', 404);
                }

                //  Restar la cantidad solicitada del stock disponible
                $productoB->cantidad_disponible -= $producto['cantidad'];
                $productoB->save();

                // Calculo de los importes
                $subTotal = $productoB->precio * $producto['cantidad'];
                $totalPagar = $totalPagar + $subTotal; // Calcula el total a pagar

                // Items de la compra
                $compraItems[] = [
                    'producto_id' => $productoB->id, // Id del producto
                    'precio' => $productoB->precio, // Precio del producto
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $subTotal,
                ];

            }

            // Registro en la tabla compra
            $compra = Compra::create([
                'subtotal' => $totalPagar,
                'total' => $totalPagar,
            ]);

            // Asociar los productos a la compra con sus cantidades y sus subtotales
            $compra->producto()->attach($compraItems);
            return ApiResponse::success('Compra realizada con exito', 201, $compra); // Respuesta exitosa

        } catch (QueryException $th) {
            // Error de consulta en la base de datos
            return ApiResponse::error('Error al realizar la compra', 500, $th->getMessage()); // Error 500
        } catch (Exception $th) {
            return ApiResponse::error('Error inesperado', 500);
        }
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
