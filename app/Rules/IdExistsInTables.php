<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class IdExistsInTables implements Rule
{
    public function passes($attribute, $value)
    {
        // Verifica si el ID existe en tabla_1 o tabla_2
        return DB::table('clients')->where('id', $value)->exists() || DB::table('providers')->where('id', $value)->exists();
    }

    public function message()
    {
        return 'La persona no se encuentra registrada.';
    }
}