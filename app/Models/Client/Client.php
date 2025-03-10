<?php

namespace App\Models\Client;

use App\Models\Account\Account;
use App\Models\Configuration\ClientSegment;
use App\Models\Configuration\Zona;
use App\Models\Stock\StockMovement;
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

    const COLUMNS = [
        'Codigo',
        'Nombre',
        'Apellido',
        'Cuit',
        'Documento',
        'Zona',
        'Consumidor Final',
        'Responsable Inscripto',
        'Monotributista'       
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
    
    public function scopeFilterAdvance($query, $search, $column){
        if($search)
        {
            switch ($column) 
            {
                case 1:
                    $query->where('code', 'like', "%$search%");                    
                    break;
                case 2:
                    $query->where('name', 'like', "%$search%");
                    break;
                case 3:
                    $query->where('surname', 'like', "%$search%");
                    break;
                case 4:
                    $query->where('cuit', 'like', "%$search%");
                    break;
                case 5:
                    $query->where('n_document', 'like', "%$search%");
                    break;
                case 6:
                    $query->whereHas('zona', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
                    break;
                case 7:
                    $query->where('client_segment_id', ClientSegment::CONSUMIDOR_FINAL);
                    break;
                case 8:
                    $query->where('client_segment_id', ClientSegment::RESPONSABLE_INSCRIPTO);
                    break;
                case 9:
                    $query->where('client_segment_id', ClientSegment::MONOTRIBUTISTA);
                    break;
            
                default:
                    $query->where('code', $search);
                    break;
            }
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

    public function stockMovements() 
    {
        return $this->morphMany(StockMovement::class, 'personable');
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
