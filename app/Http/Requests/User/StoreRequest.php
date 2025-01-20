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
            'employee_function_id'   => 'required|integer|exists:employee_functions,id',
            'zona_id'   => [
                function($attribute, $value, $fail) {
                    $functions = User::FUNCTIONS_ID_WITH_ZONA;
                    $employee_function_id = $this->input('employee_function_id');
                    if (in_array($employee_function_id, $functions) && empty($value)) {
                        $fail('Zona es un campo oblilgatorio.');
                    }
                },
                'exists:zonas,id',
            ]            
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
            'is_user.digits'    => 'El tipo de usuario no es válido.',
            'role_id.required'      => 'El Rol es un campo obligatorio.',
            'role_id.integer'       => 'El Rol no es valido',
            'role_id.exists'        => 'El Rol seleccionado no exite.',
            'employee_function_id.required'      => 'La funcion del empleado es un campo obligatorio.',
            'employee_function_id.integer'       => 'La funcion del empleado no es valido',
            'employee_function_id.exists'        => 'La funcion de empleado seleccionada no exite.',
            'zona_id.integer'       => 'La zona no es valido',
            'zona_id.exists'        => 'La zona seleccionada no exite.',
        ]; 
    }

}
