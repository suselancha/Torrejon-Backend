<?php

namespace App\Http\Requests\Sucursal;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSucursalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [            
            //'code' => 'nullable|unique:sucursales|between:1,4',
            'code' => 'nullable|between:1,4|unique:sucursales,code,'.$this->route('sucursale'),
            'nombre' => 'required|max:200',
            'client_id' => 'required|integer|exists:clients,id',
            'zona_id' => 'required|integer|exists:zonas,id',
            'state' => 'required|numeric'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }

    public function messages(): array
    {        
        return [
            'code.unique' => 'Código ya existe',
            'code.between' => 'Código entre 1 a 4 caracteres',
            'nombre.required' => 'Nombre sucursal requerida',
            'nombre.max' => 'Máximo 200 caracteres',            
            'client_id.required' => 'Cliente requerido',
            'client_id.integer' => 'Cliente, valor id no válido',
            'client_id.exists' => 'Cliente no existe',
            'zona_id.required' => 'Zona requerida',
            'zona_id.integer' => 'Zona, valor id no válido',
            'zona_id.exists' => 'Zona no existe',
            'state.required' => 'Estado requerido',
            'state.numeric' => 'Estado debe ser numérico',
        ]; 
    }
}
