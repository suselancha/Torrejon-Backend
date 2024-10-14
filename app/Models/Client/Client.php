<?php

namespace App\Models\Client;

use App\Models\Configuration\ClientSegment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "surname",
        "name",
        "full_name",
        "client_segment_id",
        "phone",
        "celular",
        "email",
        "type",
        "type_document",
        "n_document",
        "address",
        "state",
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
}
