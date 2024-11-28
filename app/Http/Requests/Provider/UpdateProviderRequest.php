<?php

namespace App\Http\Requests\Provider;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProviderRequest extends FormRequest
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
            'n_document' => 'nullable|regex:/^([0-9])*$/|min:7|max:8|unique:providers,n_document,'.$this->route('provider'),
            'email' => 'nullable|email|unique:providers,email,'.$this->route('provider'),
            'cuit' => 'nullable|regex:/^([0-9])*$/|min:11|max:11|unique:providers,cuit,'.$this->route('provider'),
            'code' => 'required|between:1,4|unique:providers,code,'.$this->route('provider'),            
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
            'n_document.unique' => 'Número documento ya existe',
            'n_document.min' => 'Número documento deber tener mínimo 7 dígitos',
            'n_document.max' => 'Número documento deber tener máximo 8 dígitos',
            'n_document.regex' => 'Ingrese sólo números',
            'email.email' => 'Dirección de correo inválida',
            'email.unique' => 'Correo ya existe',
            'cuit.unique' => 'Cuit ya existe',
            'cuit.min' => 'Cuit deber tener hasta 11 caracteres',
            'cuit.max' => 'Cuit deber tener hasta 11 caracteres',
            'cuit.regex' => 'Ingrese sólo números',
            'code.unique' => 'Código ya existe',
            'code.between' => 'Código entre 1 a 4 caracteres',
            'code.required' => 'Codigo requerido',
            'state.required' => 'Estado requerido',
            'state.numeric' => 'Estado debe ser numérico',
        ]; 
    }
}
