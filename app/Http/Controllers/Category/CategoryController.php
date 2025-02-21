<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Requests\Category\UpdateRequest;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    // Mostrar todos los registros
    public function index(Request $request)
    {
        // Obtener todas las categorías
        $categories = Category::where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);
        
        return response()->json([
            'total'     => $categories->total(),
            'categories'=> $categories->map(function($category){
                return [
                    "id" => $category->id,
                    "name" => $category->name,
                    "description" => $category->description,
                    "subcategories" => $category->subcategories
                ];
            })
        ]);
    }

    public function store(StoreRequest $request)
    {        
        try{        
            DB::beginTransaction();    
            $category = Category::create($request->all());
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
            'message' => 'Rubro Creado Correctamente.',
            'status' => 201,
            'category' => $category
        ];

        return response()->json($response, 201);
    }

    public function update(UpdateRequest $request, Category $category)
    {
        try{        
            DB::beginTransaction();
            $category->update($request->all());
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
            'message' => 'Rubro Actualizado Correctamente.',
            'status' => 200,
            'category' => $category
        ];
        return response()->json($response, 200);
    }

    public function destroy(string $id)
    {
        //Encuentra el modelo que quiero eliminar
        $category = Category::find($id);
            
        //Verifica si tengo modelos asociados
        if($category->subcategories()->count() > 0) {
            $response=[
                'success' => false,
                'message' => 'No se puede eliminar, porque tiene otros subrubros asociados.',
                'status' => 200
            ];
            return response()->json($response, 200);
        }

        //Si no tiene modelos hijos, elimino el modelo
        $category->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Rubro eliminado exitosamente',
            'status'    => 200,
            'category'  => $category
        ]);

        /* try {
        } catch (ModelNotFoundException $e) {
            //Captura si no encontro el modelo
            return response()->json([
                'success'   => false,
                'message'   => 'Rubro no encontrado.',
                'status'    => 404
            ], 404);

        } catch (QueryException $e) {
            //Captura si el error fue de BD
            return response()->json([
                'success'   => false,
                'message'   => 'Error al intentar eliminar el rubro.',
                'status'    => 500
            ], 500);

        } catch (\Exception $e) {
            //Captura cualquier tipo de error general
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage(),
                'status'    => 400
            ], 400);
        } */        
    }
}
