<?php

namespace App\Models\Client;

use App\Models\Configuration\ClientSegment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "code",
        "surname",
        "name",
        "razon_social",
        "client_segment_id",
        "phone",
        "celular",
        "email",
        "type_document",
        "n_document",
        "cuit",
        "address",
        "user_id",
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

    public function client_segment(){
        return $this->belongsTo(ClientSegment::class);
    }
    
    public function scopeFilterAdvance($query, $search, $client_segment_id, $type){
        if($search){
            // Búsqueda múltiples campos
            $query->where(DB::raw("CONCAT(clients.full_name,' ',IFNULL(clients.code,''),' ',clients.n_document)"),"like","%".$search."%");
        }
        if($client_segment_id){
            $query->where("client_segment_id",$client_segment_id);
        }
        if($type){
            $query->where("type",$type);
        }

        return $query;
    }
}
