<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Bank;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $banks = Bank::all();

        return response()->json([
            "banks" => $banks,
        ]);
    }

    public function store(Request $request)
    {
    
        try{        
            DB::beginTransaction();    
            Bank::create($request->all());
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
            'message' => 'Banco Creado Correctamente.',
            'status' => 201
        ];
        return response()->json($response, 201);
    }

    public function show(string $id)
    {
        $bank = Bank::findOrFail($id);
        
        return response()->json([
            "bank" => $bank,            
        ]);
    }


    public function update(Request $request, string $id)
    {
        try{        
            DB::beginTransaction();    
            $bank = Bank::findOrFail($id);
            $bank->update($request->all());
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
            'message' => 'Cuenta Actualizada Correctamente.',
            'status' => 200
        ];
        return response()->json($response, 201);
    }

    public function destroy(string $id)
    {
        $bank = Bank::findOrFail($id);
        
        $bank->delete();
        
        return response()->json([
            "success"   => true,
            "message"   => 'Cuenta eliminada correctamente',
            "status"    => 200
        ]);
    }
}
