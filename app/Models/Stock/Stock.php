<?php

namespace App\Models\Stock;

use App\Models\Client\Client;
use App\Models\Product\Product;
use App\Models\Provider\Provider;
use App\Models\Unit\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'product_warehouse';

    protected $fillable = [
        'product_id', 'warehouse_id', 'stock'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    
    public static function getAll()
    {
        return Product::select('products.id', 'products.name', DB::raw('SUM(stock.stock) as total_stock'))
            ->join('product_warehouse as stock', 'products.id', '=', 'stock.product_id')
            ->groupBy('products.id', 'products.name')
            ->get();
    }

    public function increaseStock($quantity, $description = null, $personable_id, $personable_type)
    {
        if ($personable_type == 'provider')
        {
            $personable_type = Provider::class;
        }elseif ($personable_type == 'client') 
        {
            $personable_type = Client::class;
        }

        DB::transaction(function () use ($quantity, $description, $personable_id, $personable_type) {
            // Actualizar stock en la tabla pivot
            DB::table('product_warehouse')
                ->where('product_id', $this->product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->increment('stock', $quantity);
        
            // Registrar el movimiento
            StockMovement::create([
                'quantity' => $quantity,
                'type' => 'in',
                'description' => $description,
                'product_id' => $this->product_id,
                'warehouse_id' => $this->warehouse_id,
                'personable_id' => $personable_id,
                'personable_type' => $personable_type,
            ]);
        });
    }

    public function decreaseStock($quantity, $description = null, $personable_id, $personable_type)
    {
        if ($personable_type == 'provider')
        {
            $personable_type = Provider::class;
        }elseif ($personable_type == 'client') 
        {
            $personable_type = Client::class;
        }

        DB::transaction(function () use ($quantity, $description, $personable_id, $personable_type) {
            // Verificar stock disponible
            $stockActual = DB::table('product_warehouse')
                ->where('product_id', $this->product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->value('stock');

            if ($stockActual < $quantity) {
                throw new HttpResponseException(response()->json([
                    'success'   => false,
                    'message'   => 'El Stock es insuficiente',
                    'status'    => 500
                ]));
            }

            // Actualizar stock en la tabla pivot
            DB::table('product_warehouse')
                ->where('product_id', $this->product_id)
                ->where('warehouse_id', $this->warehouse_id)
                ->decrement('stock', $quantity);

            // Registrar el movimiento
            StockMovement::create([
                'quantity' => $quantity,
                'type' => 'out',
                'description' => $description,
                'product_id' => $this->product_id,
                'warehouse_id' => $this->warehouse_id,
                'personable_id' => $personable_id,
                'personable_type' => $personable_type,
            ]);
        });
    }

}
