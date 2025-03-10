<?php

namespace App\Rules;

use App\Models\Stock\Stock;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStockQuantity implements ValidationRule
{
    protected $product_id;
    protected $warehouse_id;

    public function __construct($product_id, $warehouse_id)
    {
        $this->product_id = $product_id;
        $this->warehouse_id = $warehouse_id;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Buscar el stock del producto en el almacén
        $stock = Stock::where('product_id', $this->product_id)
                      ->where('warehouse_id', $this->warehouse_id)
                      ->first();

        // Si no hay stock en el almacén
        if (!$stock) {
            $fail('No hay stock disponible en este almacén.');
            return;
        }

        // Si la cantidad a reducir es mayor al stock disponible
        if ($value > $stock->stock) {
            $fail("La cantidad a extraer ($value) supera el stock disponible ({$stock->stock}).");
        }
    }
}
