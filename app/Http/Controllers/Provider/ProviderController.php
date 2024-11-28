<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\StoreProviderRequest;
use App\Http\Requests\Provider\UpdateProviderRequest;
use App\Http\Resources\Provider\ProviderCollection;
use App\Models\Provider\Provider;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        // Obtener valor via get
        $search = $request->get("search");         
        $providers = Provider::filterAdvance($search)->orderBy("id","asc")->paginate(25);

        return response()->json([
            "total" => $providers->total(),
            "providers" => ProviderCollection::make($providers),
        ]);
    }

    public function store(StoreProviderRequest $request)
    {                  
        try{        
            DB::beginTransaction();    
            Provider::create($request->all());
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
            'message' => 'Proveedor Creado Correctamente.',
            'status' => 201
        ];
        return response()->json($response, 201);
    }

    public function show(string $id)
    {
        $provider = Provider::findOrFail($id);
        return response()->json([
            "provider" => $provider,            
        ]);
    }


    public function update(UpdateProviderRequest $request, string $id)
    {        
        try{        
            DB::beginTransaction();    
            $client = Provider::findOrFail($id);
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
            'message' => 'Proveedor Actualizado Correctamente.',
            'status' => 201
        ];
        return response()->json($response, 201);
    }
}
