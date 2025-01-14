<?php

namespace App\Http\Controllers\Subcategory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subcategory\StoreRequest;
use App\Http\Requests\Subcategory\UpdateRequest;
use App\Models\Category\Category;
use App\Models\Subcategory\Subcategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubcategoryController extends Controller
{
    // Mostrar todos los registros
    public function index(Request $request)
    {
        // Obtener todas las categorías
        $subcategories = Subcategory::where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);
        
        return response()->json([
            'total'         => $subcategories->total(),
            'subcategories' => $subcategories->map(function($subcategory){
                return [
                    "id"            => $subcategory->id,
                    "name"          => $subcategory->name,
                    "description"   => $subcategory->description,
                    "category"      => $subcategory->category->name,
                ];
            })
        ]);
    }

    /**
     * Display the accounts of a client or a provider.
     */
    public function get_subcategories(Request $request)
    {   
        $category = Category::find($request->category_id);

        return response()->json([
            'category' => $category,            
            'subcategories'  => $category->subcategories->map(function($subcategory){
                return [
                    'id'            => $subcategory->id,
                    'name'          => $subcategory->name,
                    'description'   => $subcategory->description,
                    'category'      => $subcategory->category->name
                ];
            })
        ]);
    }

    public function store(StoreRequest $request)
    {        
        try{        
            DB::beginTransaction();    
            $subcategory = Subcategory::create($request->all());
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
            'message' => 'Subrubro Creado Correctamente.',
            'status' => 201,
            'subcategory' => $subcategory
        ];

        return response()->json($response, 201);
    }

    public function update(UpdateRequest $request, Subcategory $subcategory)
    {
        try{        
            DB::beginTransaction();
            $subcategory->update($request->all());
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
            'message' => 'Subrubro Actualizado Correctamente.',
            'status' => 201,
            'subcategory' => $subcategory
        ];
        return response()->json($response, 201);
    }

    public function destroy(string $id)
    {
        //Encuentra el modelo que quiero eliminar
        $subcategory = Subcategory::findOrFail($id);
            
        //Verifica si tengo modelos asociados
        if($subcategory->products()->count() > 0) {
            $response=[
                'success' => false,
                'message' => 'No se puede eliminar, porque tiene productos asociados.',
                'status' => 200
            ];
            return response()->json($response, 200);
        }

        //Si no tiene modelos hijos, elimino el modelo
        $subcategory->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Subrubro eliminado exitosamente',
            'status'    => 200,
            'subcategory' => $subcategory
        ]);
        /* try {
        } catch (ModelNotFoundException $e) {
            //Captura si no encontro el modelo
            return response()->json([
                'success'   => false,
                'message'   => 'Subrubro no encontrado.',
                'status'    => 404
            ], 404);

        } catch (QueryException $e) {
            //Captura si el error fue de BD
            return response()->json([
                'success'   => false,
                'message'   => 'Error al intentar eliminar el subrubro.',
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
