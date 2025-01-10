<?php

namespace App\Models\Account;

use App\Http\Requests\Account\StoreRequest;
use App\Models\Client\Client;
use App\Models\Configuration\Bank;
use App\Models\Provider\Provider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'alias',
        'number',
        'ubc',
        'bank_id',
        'accountable_id',
        'accountable_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function bank() {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the provider or client that owns the account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountable()
    {
        return $this->morphTo();
    }

    public static function createModel(StoreRequest $request) 
    {
        try{
            switch ($request->accountable_type) {
                case 'client':
                    $owner = Client::findOrFail($request->accountable_id);
                    break;
                case 'provider':
                    $owner = Provider::findOrFail($request->accountable_id);
                    break;                
            }

            DB::beginTransaction();

            $account = Account::create($request->all());

            $owner->accounts()->save($account);
            
            DB::commit();

            $result['success'] = true;

            $result['account'] = $account;

        } catch(\Throwable $th) {
            
            DB::rollBack();
            
            Log::info($th);
            
            $result['success'] = false;

            $result['th'] = $th;

            return $result;
        }

        return $result;

    }
}
