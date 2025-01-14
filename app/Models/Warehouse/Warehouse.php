<?php

namespace App\Models\Warehouse;

use App\Models\Product\Product;
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
        return $this->hasMany(Product::class);
    }
}
