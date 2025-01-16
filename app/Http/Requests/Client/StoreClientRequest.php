<?php

namespace App\Http\Requests\Client;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClientRequest extends FormRequest
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
            'n_document' => 'nullable|unique:clients|min:7|max:8|regex:/^([0-9])*$/',
            'email' => 'nullable|email|unique:clients',
            'cuit' => 'nullable|unique:clients|min:11|max:11|regex:/^([0-9])*$/',
            'code' => 'nullable|unique:clients|between:1,4',
            'client_segment_id' => 'required|integer|exists:client_segments,id',
            'state' => 'required|numeric',
            'zona_id' => 'nullable|integer|exists:zonas,id'
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
            'client_segment_id.required' => 'Tipo de cliente requerido',
            'client_segment_id.integer' => 'Tipo de cliente, valor id no válido',
            'client_segment_id.exists' => 'Tipo de cliente no existe',
            'state.required' => 'Estado requerido',
            'state.numeric' => 'Estado debe ser numérico',
            'zona_id.integer' => 'La opcion zona no es valida',
            'zona_id.exists' => 'La zona seleccionada no esta registrada',
        ]; 
    }
}
