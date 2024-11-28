<?php

namespace App\Models\Sucursale;

use App\Models\Client\Client;
use App\Models\Configuration\Zona;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sucursale extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "code",
        "nombre",
        "direccion",
        "telefono",
        "celular",
        "email",
        "referencia",                
        "ubigeo_provincia",
        "ubigeo_departamento",
        "ubigeo_localidad",
        "provincia",
        "departamento",
        "localidad",
        "client_id",
        "zona_id",
        "state"        
    ];

    public function setCreatedAtAttribute($value) {
        date_default_timezone_set("America/Argentina/Jujuy");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value) {
        date_default_timezone_set("America/Argentina/Jujuy");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function zona(){
        return $this->belongsTo(Zona::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function scopeFilterAdvance($query, $search, $zona_id){
        if($search){
            // Búsqueda múltiples campos
            $query->where(DB::raw("CONCAT(IFNULL(sucursales.code,''),' ',IFNULL(sucursales.nombre,''))"),"like","%".$search."%");
        }    
        
        if($zona_id){
            $query->where("zona_id",$zona_id);
        }

        return $query;
    }
}
