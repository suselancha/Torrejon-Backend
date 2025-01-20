<?php

namespace App\Models\Region;

use App\Models\Configuration\Zona;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }
}
