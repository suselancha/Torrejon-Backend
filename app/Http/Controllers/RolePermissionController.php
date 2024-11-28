<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $roles = Role::with(["permissions"])->where('name', 'like', '%'.$search.'%')->orderBy('id', 'desc')->paginate(5);

        return response()->json([
            'total' => $roles->total(),
            'roles' => $roles->map(function($rol){
                $rol->permission_pluck = $rol->permissions->pluck('name');
                $rol->created_format_at = $rol->created_at->format('Y-m-d h:i A');
                return $rol;
            }),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param name: string name of role
     * @param permissions: array permissions of role
     */
    public function store(Request $request)
    {
        $IS_ROLE = Role::where("name", $request->name)->first();

        if ($IS_ROLE)
        {
            return response()->json([
                'message' => 403,
                'message_text' => "El rol ya existe."
            ]);
        }

        $role = Role::create([
            'guard_name' => 'api',
            'name' => $request->name
        ]);

        foreach ($request->permissions as $key => $permission) {
            $role->givePermissionTo($permission);                        
        }

        return response()->json([
            'message' => 200,
            'role' => [
                'id' => $role->id,
                'permission' => $role->permissions,
                'permission_pluck' => $role->permissions->pluck('name'),
                'created_format_at' => $role->created_at->format('Y-m-d h:i A'),
                'name' => $role->name,
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
        $role_exists = Role::where("name", $request->name)->where('id','<>', $id)->first();

        if ($role_exists)
        {
            return response()
                ->json([
                    'message' => 403,
                    'message_text' => "El rol ya existe."
                ]);
        }

        $role = Role::findOrFail($id);
        $role->update($request->all());
        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 200,
            'role' => [
                'id' => $role->id,
                'permission' => $role->permissions,
                'permission_pluck' => $role->permissions->pluck('name'),
                'created_format_at' => $role->created_at->format('Y-m-d h:i A'),
                'name' => $role->name,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        // TODO: validacion por usuarios
        $role->delete();

        return response()->json([
            'message' => 200,
        ]);
        
    }
}
