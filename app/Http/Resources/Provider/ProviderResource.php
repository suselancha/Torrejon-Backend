<?php

namespace App\Http\Resources\Provider;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->resource->id,
            "code" => $this->resource->code,
            "surname" => $this->resource->surname,
            "name" => $this->resource->name,
            "razon_social" => $this->resource->razon_social,            
            "phone" => $this->resource->phone,
            "celular" => $this->resource->celular,
            "email" => $this->resource->email,                        
            "n_document" => $this->resource->n_document,
            "cuit" => $this->resource->cuit,
            "address" => $this->resource->address,            
            "state" => $this->resource->state,
            "ubigeo_provincia" => $this->resource->ubigeo_provincia,
            "ubigeo_departamento" => $this->resource->ubigeo_departamento,
            "ubigeo_localidad" => $this->resource->ubigeo_localidad,
            "provincia" => $this->resource->provincia,
            "departamento" => $this->resource->departamento,
            "localidad" => $this->resource->localidad,            
            "created_format_at" => $this->resource->created_at->format("Y-m-d h:i A")
        ];
    }
}
