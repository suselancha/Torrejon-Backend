<?php

namespace App\Http\Requests\Stock;

use App\Rules\IdExistsInTables;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IncreaseRequest extends FormRequest
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
            'quantity'          => 'required|integer',
            'personable_type'   => 'required|in:client,provider',
            'personable_id'     => ['required', 'integer', new IdExistsInTables()],
            'product_id'        => 'required|integer|exists:products,id',
            'warehouse_id'      => 'required|integer|exists:warehouses,id',
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
            'personable_type.required'  => 'El campo persona es obligatorio',
            'personable_type.in'        => 'El campo persona es invalido',
            'personable_id.required'    => 'El campo persona es oligatorio',
            'personable_id.integer'     => 'El campo persona es invalido',
            'product_id.required'       => 'El campo producto es obligatorio',
            'product_id.integer'        => 'El campo producto es invalido',
            'product_id.exists'         => 'El campo producto es inexistente',
            'warehouse_id.required'     => 'El campo almacen es obligatorio',
            'warehouse_id.integer'      => 'El campo almacen es invalido',
            'warehouse_id.exists'       => 'El campo almacen es inexistente',
        ]; 
    }
}
