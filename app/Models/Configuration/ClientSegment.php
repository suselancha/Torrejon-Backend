<?php

namespace App\Models\Configuration;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientSegment extends Model
{
    use HasFactory;
    use SoftDeletes;

    const CONSUMIDOR_FINAL = 5;
    const RESPONSABLE_INSCRIPTO = 6;
    const MONOTRIBUTISTA = 7;

    protected $fillable = [
        "name",
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
}
