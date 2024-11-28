<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'name'      => 'required|string|alpha_spaces|max:255',
            'surname'   => 'required|string|alpha_spaces|max:255',
            'email'     => 'required|unique:users|email',
            'password'  => 'required|string|confirmed|max:20',
            'is_user'   => 'required|numeric|digits:1',
            'date_entry'=> 'required|date',
            'document'  => 'nullable|unique:users|string|numeric|min_digits:7|max_digits:8',            
            'jobcode'   => 'nullable|unique:users|string|numeric|digits:11',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|numeric|min_digits:7|max_digits:15',
            'cell'      => 'nullable|string|numeric|min_digits:7|max_digits:15',
            'code'      => 'nullable|unique:users|numeric|min_digits:1|max_digits:4',
            'role_id'   => 'required|integer|exists:roles,id',
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
            'is_user.required'  => 'El tipo de usuario es requerido.',
            'is_user.numeric'   => 'El tipo de usuario debe ser numerico.',
            'is_user.digits'    => 'El tipo de usuario es de tipo logico.',
        ]; 
    }

}
