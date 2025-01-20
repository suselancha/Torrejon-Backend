<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule as ValidationRule;

class ZonaIdRule implements Rule
{
    protected $funcionId;
    protected $ignoreId;

    // Constructor opcional para pasar los parámetros
    public function __construct($funcionId, $ignoreId = null)
    {
        $this->funcionId = $funcionId;
        $this->ignoreId = $ignoreId;
    }

    // La validación
    public function passes($attribute, $value)
    {
        // Si funcion_id no está en {1, 2, 3}, no se valida zona_id
        if (!in_array($this->funcionId, [1, 2, 3])) {
            return true;
        }

        // Usamos Rule::exists con condiciones adicionales
        $query = Rule::exists('zonas', 'id');  // Asegura que el valor exista en la columna 'id' de la tabla 'zonas'
        
        // Si se pasa un 'ignoreId', lo agregamos como un filtro
        if ($this->ignoreId) {
            $query = $query->where('id', '!=', $this->ignoreId);  // Ignora el id si es el que se está editando
        }

        // Aplicamos la regla 'exists' con la consulta personalizada
        return $query->passes($attribute, $value);
    }

    // El mensaje de error
    public function message()
    {
        return 'La zona seleccionada no es válida.';
    }
}
