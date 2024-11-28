<?php

namespace App\Models\Account;

use App\Http\Requests\Account\StoreRequest;
use App\Models\Client\Client;
use App\Models\Provider\Provider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'bank',
        'alias',
        'number',
        'ubc'
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

    /**
     * Get the provider or client that owns the account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function accountable()
    {
        return $this->morphTo();
    }

    public static function createModel(StoreRequest $request) {

        switch ($request->accountable_type) {
            case 'client':
                $owner = Client::findOrFail($request->accountable_id);
                break;
            case 'provider':
                $owner = Provider::findOrFail($request->accountable_id);
                break;                
        }

        $account = Account::create($request->all());

        $owner->accounts()->save($account);

        return $account;

    }
}
