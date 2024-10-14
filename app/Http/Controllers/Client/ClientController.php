<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientCollection;
use App\Http\Resources\Client\ClientResource;
use App\Models\Client\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get("search");

        $clients = Client::where("full_name","like","%".$search."%")->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $clients->total(),
            "clients" => ClientCollection::make($clients),
        ]);
    }

    public function store(Request $request)
    {
        $is_exits_client = Client::where("full_name",$request->full_name)->first();
        if($is_exits_client){
            return response()->json([
                "message" => 403,
                "message_text" => "Los datos del cliente ya existe"
            ]);
        }
        $client = Client::create($request->all());
        return response()->json([
            "message" => 200,
            "client" => ClientResource::make($client),
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
            "client" => ClientResource::make($client),
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
