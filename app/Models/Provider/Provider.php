<?php

namespace App\Models\Provider;

use App\Models\Account\Account;
use App\Models\Product\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Provider extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "code",
        "surname",
        "name",
        "razon_social",        
        "phone",
        "celular",
        "email",
        "n_document",
        "cuit",
        "address",        
        "state",
        "ubigeo_provincia",
        "ubigeo_departamento",
        "ubigeo_localidad",
        "provincia",
        "departamento",
        "localidad"
    ];

    public function setCreatedAtAttribute($value) {
        date_default_timezone_set("America/Argentina/Jujuy");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value) {
        date_default_timezone_set("America/Argentina/Jujuy");
        $this->attributes["updated_at"] = Carbon::now();
    }

    
    public function scopeFilterAdvance($query, $search){
        if($search){
            // Búsqueda múltiples campos
            $query->where(DB::raw("CONCAT(IFNULL(providers.cuit,''),' ',IFNULL(providers.code,''),' ',IFNULL(providers.n_document,''))"),"like","%".$search."%");
        }       

        return $query;
    }

    public function accounts() {
        return $this->morphMany(Account::class, 'accountable');
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
