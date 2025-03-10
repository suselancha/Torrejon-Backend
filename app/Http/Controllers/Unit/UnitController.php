<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\Unit\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::all();

        return response()->json([
            'success'   => true,
            'units'     => $units
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $unit = Unit::create($request->all());

        return response()->json([
            'success'   => true,
            'message'   => 'Unidad agregada',
            'unit'      => $unit
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return response()->json([
            'success'   => true,
            'unit'      => $unit
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $unit->update($request->all());

        return response()->json([
            'success'   => true,
            'message'   => 'Unidad actualizada',
            'unit'      => $unit
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Unidad eliminada',
            'unit'      => $unit
        ], 200);
    }
}
