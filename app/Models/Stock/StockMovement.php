<?php

namespace App\Models\Stock;

use App\Models\Client\Client;
use App\Models\Product\Product;
use App\Models\Provider\Provider;
use App\Models\Warehouse\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'quantity', 
        'type', 
        'description', 
        'product_id', 
        'warehouse_id',
        'personable_id',
        'personable_type'
    ];

    protected $hidden = [ 'updated_at' ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function personable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public static function getClientAndProvider()
    {
        $clients = Client::select('id', 'code', 'name', 'surname', 'razon_social', DB::raw("'client' as type"));
        
        $providers = Provider::select('id', 'code', 'name', 'surname', 'razon_social', DB::raw("'provider' as type"))
            ->union($clients) // Unimos ambas consultas
            ->get();
        
        return $providers;
    }
}
