<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
        $id = $this->route('id');
        return [
            'code' => ['required', 'string', 'max:50', 
                Rule::unique('products')->whereNull('deleted_at')->ignore($this->product->id)
            ],
            'name' => ['required', 'string', 'max:100', 
                Rule::unique('products')->whereNull('deleted_at')->ignore($this->product->id)
            ], 
            'description' => 'nullable|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'unit_id' => 'required|integer|exists:units,id'
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
            'category_id.required'      => 'Categoria es un campo obligatorio.',
            'subcategory_id.required'   => 'Subcategoria es un campo obligatorio.',
            'unit_id.required'          => 'Unidad es un campo obligatorio.',
            'category_id.integer'       => 'Categoria es un campo numerico',
            'subcategory_id.integer'    => 'Subcategoria es un campo numerico',
            'unit_id.integer'           => 'Unidad es un campo numerico',
            'category_id.exists'        => 'La Categoria no se encuentra registrada.',
            'subcategory_id.exists'     => 'La Subcategoria no se encuentra registrada.',
            'unit_id.exists'            => 'La Unidad no se encuentra registrado.',
        ]; 
    }
}
