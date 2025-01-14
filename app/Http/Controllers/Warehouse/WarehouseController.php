<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warehouse\StoreRequest;
use App\Http\Requests\Warehouse\UpdateRequest;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    // Mostrar todos los registros
    public function index(Request $request)
    {
        // Obtener todas las categorías
        $warehouses = Warehouse::where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);
        
        return response()->json([
            'total'     => $warehouses->total(),
            'warehouses'=> $warehouses->map(function($warehouse){
                return [
                    "id"      => $warehouse->id,
                    "name"    => $warehouse->name,
                    "address" => $warehouse->address,
                    "phone"   => $warehouse->phone
                ];
            })
        ]);        
    }

    public function store(StoreRequest $request)
    {        
        try{        
            DB::beginTransaction();    
            $warehouse = Warehouse::create($request->all());
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
            'message' => 'Almacen Creado Correctamente.',
            'status' => 201,
            'warehouse' => $warehouse
        ];

        return response()->json($response, 201);
    }

    public function update(UpdateRequest $request, Warehouse $warehouse)
    {
        try{        
            DB::beginTransaction();
            $warehouse->update($request->all());
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
            'message' => 'Almacen Actualizado Correctamente.',
            'status' => 201,
            'warehouse' => $warehouse
        ];
        return response()->json($response, 201);
    }

    public function destroy(string $id)
    {
        //Encuentra el modelo que quiero eliminar
        $warehouse = Warehouse::findOrFail($id);
            
        //Verifica si tengo modelos asociados
        if($warehouse->products()->count() > 0) {
            $response=[
                'success' => false,
                'message' => 'No se puede eliminar, porque tiene productos almacenados.',
                'status' => 200
            ];
            return response()->json($response, 200);
        }

        //Si no tiene modelos hijos, elimino el modelo
        $warehouse->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Almacen eliminado exitosamente',
            'status'    => 200,
            'warehouse'  => $warehouse
        ]);

        /* try {
        } catch (ModelNotFoundException $e) {
            //Captura si no encontro el modelo
            return response()->json([
                'success'   => false,
                'message'   => 'Almacen no encontrado.',
                'status'    => 404
            ], 404);

        } catch (QueryException $e) {
            //Captura si el error fue de BD
            return response()->json([
                'success'   => false,
                'message'   => 'Error al intentar eliminar el almacen.',
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
