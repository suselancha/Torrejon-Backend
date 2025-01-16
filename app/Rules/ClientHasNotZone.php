<?php

namespace App\Rules;

use App\Models\Client\Client;
use Illuminate\Contracts\Validation\Rule;

class ClientHasNotZone implements Rule
{    
    public function passes($attribute, $value)
    {
        // Verifica si un cliente ya posee una zona asignada
        $client = Client::find($value);
        return is_null($client->zona_id);
    }

    public function message()
    {
        return 'El cliente posee una zona asignada.';
    }
}
