<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Models\Provider\Provider;
use App\Models\Stock\StockMovement;
use App\Models\Subcategory\Subcategory;
use App\Models\Unit\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'code', 
        'name', 
        'description', 
        'type',
        'category_id', 
        'subcategory_id',
        'provider_id', 
        'warehouse_id',
        'unit_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class)->withDefault();
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class)->withPivot('stock');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class)
            ->with(['warehouse', 'product', 'personable'])
            ->orderBy('created_at', 'desc');
    }

    public function getTotalStock()
    {
        return DB::table('product_warehouse')
            ->where('product_id', $this->id)
            ->sum('stock');
    }

    public function getStockByWarehouse()
    {
        return DB::table('product_warehouse as pw')
            ->join('warehouses as w', 'pw.warehouse_id', '=', 'w.id')
            ->where('pw.product_id', $this->id)
            ->select('w.id', 'w.name', 'w.address', 'pw.stock')
            ->get();
    }

}
