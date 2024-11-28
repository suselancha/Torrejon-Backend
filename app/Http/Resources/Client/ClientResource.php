<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            "client_segment_id" => $this->resource->client_segment_id,
            "client_segment" => $this->resource->client_segment ? [
                "id" => $this->resource->client_segment->id,
                "name" => $this->resource->client_segment->name,
            ] : NULL,
            "phone" => $this->resource->phone,
            "celular" => $this->resource->celular,
            "email" => $this->resource->email,            
            "type_document" => $this->resource->type_document,
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
