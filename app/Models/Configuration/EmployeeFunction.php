<?php

namespace App\Models\Configuration;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeFunction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "name",
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

    public function users() 
    {
        return $this->hasMany(User::class);
    }


}
