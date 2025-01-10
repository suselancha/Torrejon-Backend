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
    protected $model;

    public function __construct(Bank $bank)
    {
        $this->model = $bank;
    }

    public function index(Request $request)
    {
        $banks = Bank::where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return response()->json([
            'total' => $banks->total(),
            'banks' => $banks->map(function($bank) {
                return [
                    "id" => $bank->id,
                    "name" => $bank->name
                ]; 
            }),
        ]);        
    }

    public function store(Request $request)
    {
        $result = $this->model->createModel($request);

        if ($result['success']) {
            $response=[
                'success' => true,
                'message' => 'Banco Creado Correctamente.',
                'status' => 201,
                'bank'  => $result['bank']
            ];

            return response()->json($response, 201);
        }
        else {
            $response=[
                'success' => false,
                'message' => $result['th']->getMessage(),
                'status' => 500
            ];
            
            throw new HttpResponseException(response()->json($response, 500));
        }
        
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
        
        $bank = Bank::findOrFail($id);

        $result = $this->model->updateModel($request, $bank);

        if ($result['success']) {
            
            $response=[
                'success' => true,
                'message' => 'Banco Actualizado Correctamente.',
                'status' => 200,
                'bank'  => $result['bank']
            ];

            return response()->json($response);
        }
        else {

            $response=[
                'success' => false,
                'message' => $result['th']->getMessage(),
                'status' => 500
            ];

            throw new HttpResponseException(response()->json($response, 500));
        }
    }

    public function destroy(string $id)
    {   
        $result = $this->model->deleteModel($id);

        if ($result['success']) {
            return response()->json([
                "success"   => true,
                "message"   => 'Banco eliminado correctamente',
                "status"    => 200,
                "bank"      => $result['bank']
            ]);
        }
        else {
            $response=[
                'success' => false,
                'message' => $result['th']->getMessage(),
                'status' => 500
            ];

            throw new HttpResponseException(response()->json($response, 500));
        }        
    }
}
