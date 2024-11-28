<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\Configuration\Empresa;
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
        $users = User::where('name', 'like', '%'.$search.'%')->orderBy('id', 'desc')->paginate(25);

        return response()
            ->json([
                'total' => $users->total(),
                'users' => $users->map(function($user){
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'fullname' => $user->name.' '.$user->surname,
                        'email' => $user->email,
                        'password' => $user->password,                        
                        'role_id' => $user->role_id,
                        'roles' => $user->roles,
                        'role' => $user->role,
                        'empresa' => $user->empresa,                   
                        'created_format_at' => $user->created_at->format('Y-m-d h:i A'),
                    ];
                }),
            ]);
    }

    public function config()
    {
        return response()
            ->json([
                'roles' => Role::all(),
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
                'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'document' => $user->document,
                        'jobcode' => $user->jobcode,
                        'date_entry' => $user->date_entry ? $user->date_entry->format('d-m-Y'):'',
                        'address' => $user->address,
                        'phone' => $user->phone,
                        'cell' => $user->cell,
                        'code' => $user->code,
                        'email' => $user->email,
                        'is_user' => $user->is_user,
                        'role_id' => $user->role_id,
                        'empresa_id' => $user->empresa_id,
                        'date_entry' => $user->date_entry,
                        'created_format_at' => $user->created_at->format('d-m-Y'),
                        'role' => $user->role,
                        'roles' => $user->roles,
                ]
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $user = User::find($id);

        return response()->json([            
            'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'fullname' => $user->name.' '.$user->surname,
                        'document' => $user->document,
                        'jobcode' => $user->jobcode,
                        'date_entry_format_at' => $user->date_entry->format('Y-m-d'),
                        'address' => $user->address,
                        'phone' => $user->phone,
                        'cell' => $user->cell,
                        'code' => $user->code,
                        'email' => $user->email,
                        'password' => $user->password,
                        'is_user' => $user->is_user,
                        'role_id' => $user->role_id,
                        'empresa_id' => $user->empresa_id,
                        'date_entry' => $user->date_entry,
                        'created_format_at' => $user->created_at->format('d-m-Y'),
                        'role' => $user->role,
                        'roles' => $user->roles,
                ]
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

        $user->update();

        return response()
            ->json([
                'message' => 200,
                'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,                        
                        'role_id' => $user->role_id,
                        'role' => $user->role,
                        'roles' => $user->roles,
                        'empresa_id' => $user->empresa_id,                        
                        'created_format_at' => $user->created_at->format('Y-m-d h:i A'),
                ]
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

}
