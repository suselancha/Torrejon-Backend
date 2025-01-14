<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Models\Provider\Provider;
use App\Models\Subcategory\Subcategory;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'warehouse_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
