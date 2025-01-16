<?php

namespace App\Models\Client;

use App\Models\Account\Account;
use App\Models\Configuration\ClientSegment;
use App\Models\Configuration\Zona;
use App\Models\Sucursale\Sucursale;
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
        //"user_id",
        "state",
        "ubigeo_provincia",
        "ubigeo_departamento",
        "ubigeo_localidad",
        "provincia",
        "departamento",
        "localidad",
        "zona_id"
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
    
    public function scopeFilterAdvance($query, $search, $client_segment_id){
        if($search){
            // Búsqueda múltiples campos
            //$query->where(DB::raw("CONCAT(IFNULL(clients.cuit,''),' ',IFNULL(clients.code,''),' ',IFNULL(clients.n_document,''))"),"like","%".$search."%");

            $query->where('name', 'like', "%$search%")
            ->orWhere('surname', 'like', "%$search%")
            ->orWhere('cuit', 'like', "%$search%")
            ->orWhere('code', 'like', "%$search%")
            ->orWhere('n_document', 'like', "%$search%")
            ->orWhereHas('zona', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        }
        
        if($client_segment_id){
            $query->where("client_segment_id",$client_segment_id);
        }

        return $query;
    }

    public function scopeFilterSucursal($query, $code, $n_document, $surname){
        if($code){
            $query->where("code","like","%".$code."%")->where("state",1);
        }
        if($n_document){
            $query->where(DB::raw("CONCAT(IFNULL(clients.cuit,''),' ',IFNULL(clients.n_document,''))"),"like","%".$n_document."%")->where("state",1);
        }
        if($surname){
            $query->where(DB::raw("CONCAT(IFNULL(clients.surname,''),' ',IFNULL(clients.razon_social,''))"),"like","%".$surname."%")->where("state",1);
            //$query->where("surname","like","%".$surname."%");
        }
    }

    public function accounts() 
    {
        return $this->morphMany(Account::class, 'accountable');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function sucursales()
    {
        return $this->hasMany(Sucursale::class);
    }
}
