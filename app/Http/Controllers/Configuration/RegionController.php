<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Region\StoreRequest;
use App\Http\Requests\Region\UpdateRequest;
use App\Models\Region\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get("search");

        $regions = Region::where("name","like","%".$search."%")->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $regions->total(),
            "regions" => $regions->map(function($region){
                return $this->get_region($region);
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $region = Region::create($request->all());
        
        return response()->json([
            "success" => true,
            "message" => 'La region fue creada exitosamente.',
            "status" => 201,
            "region" => $this->get_region($region),
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Region $region)
    {
        $region->update($request->all());
        
        return response()->json([
            "success" => true,
            "message" => 'La region fue actualizada exitosamente.',
            "status" => 201,
            "region" => $this->get_region($region),
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Encuentra el modelo que quiero eliminar
        $region = Region::findOrFail($id);
            
        //Verifica si tengo modelos asociados
        if($region->zonas()->exists()) {
            $response=[
                'success' => false,
                'message' => 'La region '.$region->name.' tiene zonas asociadas.',
                'status' => 200
            ];
            return response()->json($response, 200);
        }

        //Si no tiene modelos hijos, elimino el modelo
        $region->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Region eliminada exitosamente',
            'status'    => 200,
            'region'  => $this->get_region($region)
        ]);
    }

    private function get_region($region) {
        return [
            "id" => $region->id,
            "name" => $region->name,
            "description" => $region->description
        ];
    }
}

