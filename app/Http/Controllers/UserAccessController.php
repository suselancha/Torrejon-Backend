<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                        'email' => $user->email,
                        'password' => $user->password,
                        'surname' => $user->surname,
                        'full_name' => $user->name." ".$user->surname,
                        'phone' => $user->phone,
                        'type_document' => $user->type_document,
                        'n_document' => $user->n_document,
                        'address' => $user->address,
                        'gender' => $user->gender,
                        'role' => $user->role,
                        'role_id' => $user->role_id,
                        'sucursal_id' => $user->sucursal_id,
                        'avatar' => $user->avatar ? env('APP_URL').'storage/'.$user->avatar : 'https://cdn-icons-png.flaticon.com/128/3135/3135715.png',
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
    public function store(Request $request)
    {
        $USER_EXISTS = User::where("email", $request->email)->first();

        if ($USER_EXISTS)
        {
            return response()
                ->json([
                    'message' => 403,
                    'message_text' => "El usuario ya existe."
                ]);
        }

        if ($request->hasFile("image")) 
        {
            $path = Storage::putFile("users", $request->file("image"));

            $request->request->add(["avatar" => $path]);
        }

        if($request->password)
        {
            $request->request->add(["password" => bcrypt($request->password)]);
        }

        $role = Role::findOrFail($request->role_id);

        $user = User::create($request->all());

        $user->assignRole($role);

        return response()
            ->json([
                'message' => 200,
                'user' => [
                    'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,
                        'surname' => $user->surname,
                        'phone' => $user->phone,
                        'type_document' => $user->type_document,
                        'n_document' => $user->n_document,
                        'address' => $user->address,
                        'gender' => $user->gender,
                        'role' => $user->role,
                        'role_id' => $user->role_id,
                        'sucursal_id' => $user->sucursal_id,
                        'avatar' => $user->avatar ? env('APP_URL').'storage/'.$user->avatar : 'https://cdn-icons-png.flaticon.com/128/3135/3135715.png',
                        'created_format_at' => $user->created_at->format('Y-m-d h:i A'),
                ]
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()
            ->json([
                'role' => $role
            ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $USER_EXISTS = User::where("email", $request->email)->where("id", "<>", $id)->first();

        if ($USER_EXISTS)
        {
            return response()
                ->json([
                    'message' => 403,
                    'message_text' => "El usuario ya existe."
                ]);
        }

        $user = User::findOrFail($id);

        if ($request->hasFile("image")) 
        {
            if ($user->avatar)
            {
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users", $request->file("image"));

            $request->request->add(["avatar" => $path]);
        }

        if($request->password)
        {
            $request->request->add(["password" => bcrypt($request->password)]);
        }

        if ($request->role_id != $user->role_id)
        {
            $role_old = Role::findOrFail($user->role_id);
            $user->removeRole($role_old);

            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role);

        }

        $user->update($request->all());

        return response()
            ->json([
                'message' => 200,
                'user' => [
                    'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,
                        'surname' => $user->surname,
                        'phone' => $user->phone,
                        'type_document' => $user->type_document,
                        'n_document' => $user->n_document,
                        'address' => $user->address,
                        'gender' => $user->gender,
                        'role' => $user->role,
                        'role_id' => $user->role_id,
                        'sucursal_id' => $user->sucursal_id,
                        'avatar' => $user->avatar ? env('APP_URL').'storage/'.$user->avatar : 'https://cdn-icons-png.flaticon.com/128/3135/3135715.png',
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

        if($user->avatar) {
            Storage::delete($user->avatar);
        }        
        
        if ($user->delete())
        {
            return response()
                ->json([
                    'message' => 200,
                    'message_text' => "El rol fue eliminado."
                ]);
        }
        else{
            return response()
                ->json([
                    'message' => 400,
                    'message_text' => "El rol no puedo ser eliminado."
                ]);
        }
        
    }

}
