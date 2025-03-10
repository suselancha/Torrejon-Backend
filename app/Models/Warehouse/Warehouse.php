<?php

namespace App\Models\Warehouse;

use App\Models\Product\Product;
use App\Models\Stock\StockMovement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable= [
        'name', 'address', 'phone'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stock');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

}
