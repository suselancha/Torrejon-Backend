<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\StoreRequest;
use App\Http\Requests\Account\UpdateRequest;
use App\Models\Account\Account;
use App\Models\Client\Client;
use App\Models\Provider\Provider;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $accounts = Account::where('name', 'like', '%'.$search.'%')
            ->orWhere('bank', 'like', '%'.$search.'%')
            ->orWhere('alias', 'like', '%'.$search.'%')
            ->orWhere('ubc', 'like', '%'.$search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);

        return response()
            ->json([
                'total'     => $accounts->total(),
                'accounts'  => $accounts->map(function($account){
                    return [
                        'id'        => $account->id,
                        'name'      => $account->name,
                        'bank'      => $account->bank,
                        'alias'     => $account->alias,
                        'number'    => $account->number,
                        'ubc'       => $account->ubc,
                    ];
                }),
            ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param name: string name of account
     * @param bank: string bank of account
     * @param alias: string alias of account
     * @param ubc: string ubc of account
     * @param number: string number of account
     * @param owner_type: owner type of account
     * @param owner_id: owner identity of account
     */
    public function store(StoreRequest $request)
    {
        DB::beginTransaction();

        try{        
            
            $account = Account::createModel($request);
            
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
            'message' => 'Cuenta bancaria creada correctamente.',
            'status' => 201,
            'account' => $account
        ];

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $account = Account::find($id);

        return response()->json([            
            'account' => $account
        ]);
    }

    /**
     * Display the accounts of a client or a provider.
     */
    public function get_accounts(Request $request)
    {   
        if ($request->accountable_type == "client") {
            $accountable = Client::find($request->accountable_id);
        }
        elseif ($request->accountable_type == "provider") {
            $accountable = Provider::find($request->accountable_id);
        }

        return response()->json([
            'accountable' => $accountable,            
            'accounts' => $accountable->accounts
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Account $account)
    {           
        //$user = Account::findOrFail($account->id);        

        $account->update($request->all());

        $response=[
            'success'   => true,
            'message'   => 'Cuenta bancaria acualizada correctamente.',
            'status'    => 200,
            'account'   => $account
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = Account::findOrFail($id);
        
        $account->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Cuenta bancaria eliminada correctamente',
            'status'    => 200,
            'account'   => $account
        ]);
    }
}
