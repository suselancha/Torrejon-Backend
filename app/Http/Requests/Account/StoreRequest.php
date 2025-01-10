<?php

namespace App\Http\Requests\Account;

use App\Models\Account\Account;
use App\Rules\IdExistsInTables;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'name'      => [
                'required', 'string', 'max:255', 
                Rule::unique('accounts')->whereNull('deleted_at')
            ],
            'alias'     => [
                'required', 'string', 'max:255', 
                Rule::unique('accounts')->whereNull('deleted_at')
            ],
            'ubc'       => [
                'required', 'string', 'numeric', 'digits:22',
                Rule::unique('accounts')->whereNull('deleted_at')
            ],
            'number'    => [
                'required', 'string', 'numeric', 'min_digits:8', 'max_digits:12',
                Rule::unique('accounts')->whereNull('deleted_at')
            ],
            'bank_id'   => 'required|integer|exists:banks,id',
            'accountable_type'=> 'required|in:client,provider',
            'accountable_id'  => [
                'required',
                'integer',
                new IdExistsInTables(),
            ],            
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
            'accountable_type.required'   => 'El tipo de titular es un campo obligatorio.',
            'accountable_type.in'         => 'El tipo de titular no es valido.',
            'accountable_id.required'     => 'El titular de la cuenta es un campo obligatorio.',
            'accountable_id.integer'      => 'El titular de la cuenta no es valido.',
            'bank_id.exists'              => 'El banco no se encuentra registrado.',
        ]; 
    }
}
