<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\DecreaseRequest;
use App\Http\Requests\Stock\IncreaseRequest;
use App\Models\Product\Product;
use App\Models\Stock\Stock;
use App\Models\Stock\StockMovement;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'desc')
            ->paginate(25);
        
        return response()->json([
            'success'       => true,
            'total'         => $products->total(),
            'products'      => $products->map(function($product) {
                return [
                    'product_id'=> $product->id,
                    'code'      => $product->code,
                    'name'      => $product->name,
                    'stock'     => $product->getTotalStock(),
                    'unit'      => $product->unit->name 
                ]; 
            }),
            'warehouses'    => Warehouse::all(),
            'persons'       => StockMovement::getClientAndProvider()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $stock = Stock::create($request->all());

        return response()->json([
            'success'   => true,
            'message'   => 'Stock agregado',
            'stock'      => $stock
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = Stock::find($id);

        return response()->json([
            'success'   => true,
            'stock'      => $stock
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stock = Stock::find($id);

        $stock->update($request->all());

        return response()->json([
            'success'   => true,
            'message'   => 'Stock actualizado',
            'stock'      => $stock
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::find($id);

        $stock->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Stock eliminado',
            'unit'      => $stock
        ], 200);
    }

    public function get_persons(Request $request)
    {
        $persons = StockMovement::getClientAndProvider();
        
        return response()->json([
            'success'   => true,
            'persons'      => $persons
        ], 200);
    }
    
    public function get_details(string $id)
    {
        $rules = ['id' => 'required|integer|exists:products,id'];

        $messages = [
            'id.required'   => 'El campo producto es obligatorio',
            'id.integer'    => 'El campo producto es invalido',
            'id.exists'     => 'El campo producto es inexistente',
        ];

        $validator = Validator::make(['id' => $id], $rules, $messages);

        if ($validator->fails())
        {
            return response()->json([
                'success'   => false,
                'errors'    => $validator->errors()
            ], 422);
        }

        $product = Product::find($id);
        
        return response()->json([
            'success'   => true,
            'product'   => [
                'id'            => $product->id,
                'code'          => $product->code,
                'name'          => $product->name,
                'unit'          => $product->unit->name,
                'totalStock'    => $product->getTotalStock(),
                'warehouses'    => $product->getStockByWarehouse(),
                'movements'     => $product->movements,
            ]
        ], 200);
    }

    public function increase(IncreaseRequest $request)
    {
        $stock = Stock::firstOrCreate([
            'product_id' => $request->product_id, 
            'warehouse_id' => $request->warehouse_id
        ]);

        $stock->increaseStock($request->quantity, $request->description, $request->personable_id, $request->personable_type);
        
        return response()->json([
            'success'   => true,
            'message'   => 'Cantidad agregada',
            'product'      => [
                    'product_id'=> $stock->product->id,
                    'code'      => $stock->product->code,
                    'name'      => $stock->product->name,
                    'stock'     => $stock->product->getTotalStock(),
                    'unit'      => $stock->product->unit->name 
            ],
        ], 200);        
    }

    public function decrease(DecreaseRequest $request)
    {
        $stock = Stock::where([
            ['product_id', $request->product_id],
            ['warehouse_id', $request->warehouse_id]
        ])->first();

        $stock->decreaseStock($request->quantity, $request->description, $request->personable_id, $request->personable_type);

        return response()->json([
            'success'   => true,
            'message'   => 'Cantidad removida',
            'product'      => [
                    'product_id'=> $stock->product->id,
                    'code'      => $stock->product->code,
                    'name'      => $stock->product->name,
                    'stock'     => $stock->product->getTotalStock(),
                    'unit'      => $stock->product->unit->name 
            ],
        ], 200);
    }


}
