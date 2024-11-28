<?php

namespace App\Http\Controllers\Client;

use App\Classes\ApiResponseClass;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\Client\ClientCollection;
use App\Imports\ClientsImport;
use App\Models\Client\Client;
use App\Models\Configuration\ClientSegment;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        // Obtener valor via get
        // $search = $request->get("search");

        // Obtener valor via post
        $search = $request->search;
        $client_segment_id = $request->client_segment_id;        

        //where("full_name","like","%".$search."%")->
        $clients = Client::filterAdvance($search,$client_segment_id)->orderBy("id","asc")->paginate(25);

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

    public function import_clients(Request $request)
    {
        $request->validate([
            "import_file" => 'required|file|mimes:xls,xlsx,csv'
        ]);
        
        $path = $request->file("import_file");
        $data = Excel::import(new ClientsImport,$path);

        return response()->json([
            "message" => 200
        ]);

    }

    public function store(StoreClientRequest $request)
    {
        // try {
        //     $validator = Validator::make($request->all(), [
        //         'n_document' => 'unique:clients|min:7|max:8',
        //         'email' => 'email|unique:clients',
        //         'cuit' => 'unique:clients|min:13|max:13',
        //         'code' => 'unique:clients'
        //     ]);
    
        //     if($validator->fails()){
        //         $data = [
        //             'message' => 'Error en la validacion de datos',
        //             'errors' => $validator->errors(),
        //             'status' => 400
        //         ];
        //         return response()->json($data, 400);
        //     }
        //     $request->request->add(["user_id" => auth("api")->user()->id]);
        //     Client::create($request->all());
        //     return response()->json([
        //          "message" => 200,            
        //     ]);
        // } catch (ValidationException $e) {
        //     return response()->json($e->validator->errors(), 422); // 422: Unprocessable Entity
        // }                
        try{        
            DB::beginTransaction();    
            Client::create($request->all());
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
            'message' => 'Cliente Creado Correctamente.',
            'status' => 201
        ];
        return response()->json($response, 201);
    }

    public function show(string $id)
    {
        $client = Client::findOrFail($id);
        return response()->json([
            "client" => $client,            
        ]);
    }


    public function update(UpdateClientRequest $request, string $id)
    {
        // $is_exits_client = Client::where("full_name",$request->full_name)
        //                             ->where("id","<>",$id)->first();
        // if($is_exits_client){
        //     return response()->json([
        //         "message" => 403,
        //         "message_text" => "Los datos del cliente ya existe"
        //     ]);
        // }
        // $client = Client::findOrFail($id);
        // $client->update($request->all());
        // return response()->json([
        //     "message" => 200,
        // ]);

        try{        
            DB::beginTransaction();    
            $client = Client::findOrFail($id);
            $client->update($request->all());
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
            'message' => 'Cliente Actualizado Correctamente.',
            'status' => 201
        ];
        return response()->json($response, 201);
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
