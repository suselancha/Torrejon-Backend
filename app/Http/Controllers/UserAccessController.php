<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\Configuration\EmployeeFunction;
use App\Models\Configuration\Empresa;
use App\Models\Configuration\Zona;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserAccessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $users = User::where('name', 'like', '%'.$search.'%')
            ->orWhere('surname', 'like', '%'.$search.'%')
            ->orWhere('document', 'like', '%'.$search.'%')
            ->orWhere('jobcode', 'like', '%'.$search.'%')
            ->orWhere('code', 'like', '%'.$search.'%')
            ->orWhereHas('role', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
            ->orWhereHas('employee_function', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
            ->orWhereHas('zona', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
            ->orderBy('id', 'desc')
            ->paginate(25);

        return response()
            ->json([
                'total' => $users->total(),
                'users' => $users->map(function($user){
                    if ($user->role_id != 1){
                        return $this->get_array_user($user);
                    }                    
                }),
            ]);
    }

    public function config()
    {
        return response()
            ->json([
                'roles' => Role::all(),
                'functions' => EmployeeFunction::all(),
                'zonas' => Zona::all(),
            ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param email: string email of user
     * @param permissions: array permissions of role
     */
    public function store(StoreRequest $request)
    {
        $request->request->add(["password" => bcrypt($request->password)]);

        $request->request->add(["empresa_id" => Empresa::EMPRESA_ID]);

        $role = Role::findOrFail($request->role_id);
        $user = User::create($request->all());
        $user->assignRole($role);

        return response()
            ->json([
                'message' => 200,
                'user' => $this->get_array_user($user)
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $user = User::find($id);

        return response()->json([
            'user' => $this->get_array_user($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, User $user)
    {   
        $USER_EXISTS = User::where("email", $request->email)
            ->where("id","<>",$user->id)
            ->first();
                
        if ($USER_EXISTS)
        {
            return response()
                ->json([
                    'message' => 403,
                    'message_text' => "El empleado ya existe."
                ]);
        }

        $user = User::findOrFail($user->id);

        if($request->password)
        {
            $request->request->add(["password" => bcrypt($request->password)]);

            $user->password = $request->password;
        }

        if($request->role_id != $user->role_id){
            // Vieja Rol
            $role_old = Role::findOrFail($user->role_id);
            $user->removeRole($role_old);

            // Nuevo rol
            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role);
        }

        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->document = $request->document;
        $user->jobcode = $request->jobcode;
        $user->date_entry = $request->date_entry;
        $user->phone = $request->phone;
        $user->cell = $request->cell;
        $user->code = $request->code;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->is_user = $request->is_user;
        $user->role_id = $request->role_id;
        $user->employee_function_id = $request->employee_function_id;
        $functions_with_zona = User::FUNCTIONS_ID_WITH_ZONA;
        $user->zona()->dissociate();
        if (in_array($request->employee_function_id, $functions_with_zona))
        {
            $zona = Zona::find($request->zona_id);
            $user->zona()->associate($zona);
        }

        $user->update();

        return response()
            ->json([
                'message' => 200,
                'user' => $this->get_array_user($user)
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        
        $user->delete();

        return response()->json([
            'message'   => 200,
            'user'      => $user
        ]);
    }

    private function get_array_user($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'fullname' => $user->name.', '.$user->surname,
            'document' => $user->document,
            'jobcode' => $user->jobcode,
            'date_entry' => $user->date_entry->format('Y-m-d'),
            'address' => $user->address,
            'phone' => $user->phone,
            'cell' => $user->cell,
            'code' => $user->code,
            'email' => $user->email,
            'is_user' => $user->is_user,
            'role_id' => $user->role_id,
            'empresa_id' => $user->empresa_id,
            'created_format_at' => $user->created_at->format('d-m-Y'),
            'role' => $user->role,
            'roles' => $user->roles,
            'employee_function_id' => $user->employee_function_id,
            'employee_function' => $user->employee_function,
            'zona_id' => $user->zona_id,
            'zona' => $user->zona
        ];
    }

}
