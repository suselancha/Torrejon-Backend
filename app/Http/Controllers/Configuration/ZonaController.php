<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Zona;
use Illuminate\Http\Request;

class ZonaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get("search");

        $zonas = Zona::where("name","like","%".$search."%")->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $zonas->total(),
            "zonas" => $zonas->map(function($zona) {
                return [
                    "id" => $zona->id,
                    "name" => $zona->name,
                    "location" => $zona->location,
                    "description" => $zona->description,
                    "state" => $zona->state,
                    "created_format_at" => $zona->created_at->format("Y-m-d H:i A")
                ];
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_exits_zona = Zona::where("name",$request->name)->first();
        if($is_exits_zona){
            return response()->json([
                "message" => 403,
                "message_text" => "El nombre de la zona ya existe"
            ]);
        }
        $zona = Zona::create($request->all());
        return response()->json([
            "message" => 200,
            "zona" => [
                "id" => $zona->id,
                "name" => $zona->name,
                "location" => $zona->location,
                "description" => $zona->description,
                "state" => $zona->state ?? 1,
                "created_at" => $zona->created_at->format("Y-m-d H:i A")
            ],
        ]);
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
        $is_exits_zona = Zona::where("name",$request->name)
                                    ->where("id","<>",$id)
                                    ->whereNotNull("deleted_at")
                                    ->first();
        if($is_exits_zona){
            return response()->json([
                "success" => false,
                "message" => "El nombre de la zona ya existe",
                "status" => 403,
            ]);
        }
        $zona = Zona::findOrFail($id);
        $zona->update($request->all());
        return response()->json([
            "success" => true,
            "message" => 'Zona actualizada exitosamente',
            "status"  => 200,
            "zona" => [
                "id" => $zona->id,
                "name" => $zona->name,
                "location" => $zona->location,
                "description" => $zona->description,
                "state" => $zona->state ?? 1,
                "created_at" => $zona->created_at->format("Y-m-d H:i A")
            ],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Encuentra el modelo que quiero eliminar
        $zona = Zona::findOrFail($id);
            
        //Verifica si tengo modelos asociados
        if($zona->clients()->count() > 0 || $zona->sucursales()->count() > 0) {
            $response=[
                'success' => false,
                'message' => 'No se puede eliminar, porque tiene clientes o sucursales asociados.',
                'status' => 200
            ];
            return response()->json($response, 200);
        }

        //Si no tiene modelos hijos, elimino el modelo
        $zona->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Zona eliminada exitosamente',
            'status'    => 200,
            'zona'  => $zona
        ]);
    }
}
