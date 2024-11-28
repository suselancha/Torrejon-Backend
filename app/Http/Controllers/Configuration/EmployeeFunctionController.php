<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\EmployeeFunction;
use Illuminate\Http\Request;

class EmployeeFunctionController extends Controller
{
    public function index(Request $request)
    {
        $search =$request->get("search");

        $employee_functions = EmployeeFunction::where("name","like","%".$search."%")->orderBy("id","desc")->paginate(25);

        return response()->json([
            "total" => $employee_functions->total(),
            "employee_functions" => $employee_functions->map(function($employee_function) {
                return [
                    "id" => $employee_function->id,
                    "name" => $employee_function->name,
                    "state" => $employee_function->state,
                    "created_format_at" => $employee_function->created_at->format("Y-m-d h:i A")
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $is_exits_employee_function = EmployeeFunction::where("name",$request->name)->first();
        if($is_exits_employee_function) {
            return response()->json([
                "message" => 403,
                "message_text" => "El nombre de esa función ya existe"
            ]);
        }
        $employee_function = EmployeeFunction::create($request->all());
        return response()->json([
            "message" => 200,
            "employee_function" => [
                "id" => $employee_function->id,
                "name" => $employee_function->name,
                "state" => $employee_function->state ?? 1,
                "created_format_at" => $employee_function->created_at->format("Y-m-d h:i A")
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $is_exits_employee_function = EmployeeFunction::where("name",$request->name)
                                        ->where("id","<>",$id)->first();
        if($is_exits_employee_function) {
            return response()->json([
                "message" => 403,
                "message:text" => "El nombre de esa función ya existe"
            ]);
        }
        $employee_function = EmployeeFunction::findOrFail($id);
        $employee_function->update($request->all());
        return response()->json([
            "message" => 200,
            "employee_function" => [
                "id" => $employee_function->id,
                "name" => $employee_function->name,
                "state" => $employee_function->state ?? 1,
                "created_format_at" => $employee_function->created_at->format("Y-m-d h:i A")
            ],
        ]);
    }

    public function destroy(string $id)
    {
        $employee_function = EmployeeFunction::findOrFail($id);
        $employee_function->delete();
        return response()->json([
            "message" => 200,
        ]);
    }
}
