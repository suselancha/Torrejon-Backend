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
            "full_name" => $this->resource->full_name,
            "client_segment_id" => $this->resource->client_segment_id,
            "client_segment" => $this->resource->client_segment ? [
                "id" => $this->resource->client_segment->id,
                "name" => $this->resource->client_segment->name,
            ] : NULL,
            "phone" => $this->resource->phone,
            "celular" => $this->resource->celular,
            "email" => $this->resource->email,
            "type" => $this->resource->type,
            "type_document" => $this->resource->type_document,
            "n_document" => $this->resource->n_document,
            "address" => $this->resource->address,
            "user_id" => $this->resource->user_id,
            "ubigeo_provincia" => $this->resource->ubigeo_provincia,
            "ubigeo_departamento" => $this->resource->ubigeo_departamento,
            "ubigeo_localidad" => $this->resource->ubigeo_localidad,
            "provincia" => $this->resource->provincia,
            "departamento" => $this->resource->departamento,
            "localidad" => $this->resource->localidad,
            "state" => $this->resource->state,
            "created_format_at" => $this->resource->created_at->format("Y-m-d h:i A")
        ];
    }
}
