<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Models\Category\Category;
use App\Models\Product\Product;
use App\Models\Provider\Provider;
use App\Models\Subcategory\Subcategory;
use App\Models\Unit\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // Mostrar todos los registros
    public function index(Request $request)
    {
        // Obtener todos los modelos
        $products = Product::where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);
        
        return response()->json([
            'total'         => $products->total(),
            'products' => $products->map(function($product){
                return [
                    "id"            => $product->id,
                    "code"          => $product->code,
                    "name"          => $product->name,
                    "description"   => $product->description
                ];
            })
        ]);
    }

    public function show(Product $product)
    {
        return response()->json(["product" => $product]);
    }

    public function config()
    {
        return response()
            ->json([
                'categories' => Category::all(),
                'subcategories' => Subcategory::all(),
                'warehouses'    => Warehouse::all(),
                'providers'     => Provider::all(),
                'units'         => Unit::all()
            ]);
    }

    public function store(StoreRequest $request)
    {        
        try{        
            DB::beginTransaction();    
            $product = Product::create($request->all());
            DB::commit();            
        }
        catch(\Throwable $th) {
            DB::rollBack();
            Log::info($th);
            $response=[
                'success' => false,
                'message' => $th->getMessage(),
                'status' => 500
            ];
            throw new HttpResponseException(response()->json($response, 500));
        }

        $response=[
            'success' => true,
            'message' => 'Producto Creado Correctamente.',
            'status' => 201,
            'product' => $product
        ];

        return response()->json($response, 201);
    }

    public function update(UpdateRequest $request, Product $product)
    {
        try{        
            DB::beginTransaction();
            $product->update($request->all());
            DB::commit();            
        } catch(\Throwable $th) {
            DB::rollBack();
            Log::info($th);
            $response=[
                'success' => false,
                'message' => $th->getMessage(),
                'status' => 500
            ];
            throw new HttpResponseException(response()->json($response, 500));
        }
        $response=[
            'success' => true,
            'message' => 'Producto Actualizado Correctamente.',
            'status' => 201,
            'product' => $product
        ];
        return response()->json($response, 201);
    }

    public function destroy(string $id)
    {
        //Encuentra el modelo que quiero eliminar
        $product = Product::findOrFail($id);

        //Si no tiene modelos asociados, elimino el modelo
        $product->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Producto eliminado exitosamente',
            'status'    => 200,
            'product'  => $product
        ]);

        /* 
        try {
            //Encuentra el modelo que quiero eliminar
            $product = Product::findOrFail($id);

            //Verifica si tengo modelos asociados
            if($product->category || $product->subcategory || $product->provider || $product->warehouse) {
                $response=[
                    'success' => false,
                    'message' => 'No se puede eliminar, porque tiene elementos asociados.',
                    'status' => 200
                ];
                return response()->json($response, 200);
            }
            
            //Si no tiene modelos asociados, elimino el modelo
            $product->delete();

            return response()->json([
                'success'   => true,
                'message'   => 'Producto eliminado exitosamente',
                'status'    => 200,
                'product'  => $product
            ]);

        } catch (ModelNotFoundException $e) {
            //Captura si no encontro el modelo
            return response()->json([
                'success'   => false,
                'message'   => 'Producto no encontrado.',
                'status'    => 404
            ], 404);

        } catch (QueryException $e) {
            //Captura si el error fue de BD
            return response()->json([
                'success'   => false,
                'message'   => 'Error al intentar eliminar el producto.',
                'status'    => 500
            ], 500);

        } catch (\Exception $e) {
            //Captura cualquier tipo de error general
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage(),
                'status'    => 400
            ], 400);
        }
         */
    }
}

