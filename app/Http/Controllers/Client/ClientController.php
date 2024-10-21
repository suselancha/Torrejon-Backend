<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientCollection;
use App\Http\Resources\Client\ClientResource;
use App\Models\Client\Client;
use App\Models\Configuration\ClientSegment;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        // Obtener valor via get
        // $search = $request->get("search");

        // Obtener valor via post
        $search = $request->search;
        $client_segment_id = $request->client_segment_id;
        $type = $request->type;

        //where("full_name","like","%".$search."%")->
        $clients = Client::filterAdvance($search,$client_segment_id,$type)->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $clients->total(),
            "clients" => ClientCollection::make($clients),
        ]);
    }

    public function config()
    {
        $client_segments = ClientSegment::where("state",1)->get();

        return response()->json([
            "client_segments" => $client_segments
        ]);
    }

    public function store(Request $request)
    {
        // TODO: FALTA VALIDAR POR DNI Y POR CODIGO
        $is_exits_client = Client::where("full_name",$request->full_name)->first();
        if($is_exits_client){
            return response()->json([
                "message" => 403,
                "message_text" => "Los datos del cliente ya existe"
            ]);
        }
        $request->request->add(["user_id" => auth()->user()->id]);
        $client = Client::create($request->all());
        return response()->json([
            "message" => 200,            
        ]);
    }

    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return response()->json([
            "client" => $client,            
        ]);
    }


    public function update(Request $request, string $id)
    {
        $is_exits_client = Client::where("full_name",$request->full_name)
                                    ->where("id","<>",$id)->first();
        if($is_exits_client){
            return response()->json([
                "message" => 403,
                "message_text" => "Los datos del cliente ya existe"
            ]);
        }
        $client = Client::findOrFail($id);
        $client->update($request->all());
        return response()->json([
            "message" => 200,
        ]);
    }

    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json([
            "message" => 200,
        ]);
    }
}
