<?php

namespace App\Models\Configuration;

use App\Models\Client\Client;
use App\Models\Region\Region;
use App\Models\Sucursale\Sucursale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        "name",
        "location",
        "description",
        "state",
        "region_id"
    ];

    public function setCreatedAtAttribute($value) {
        date_default_timezone_set("America/Argentina/Jujuy");
        $this->attributes["created_at"] = Carbon::now();
    }

    public function setUpdatedAtAttribute($value) {
        date_default_timezone_set("America/Argentina/Jujuy");
        $this->attributes["updated_at"] = Carbon::now();
    }

    public function clients() {
        return $this->hasMany(Client::class);
    }

    public function sucursales() {
        return $this->hasMany(Sucursale::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_zona');
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }
}
