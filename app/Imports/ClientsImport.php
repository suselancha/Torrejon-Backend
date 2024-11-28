<?php

namespace App\Imports;

use App\Models\Client\Client;
use App\Models\Configuration\ClientSegment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClientsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsErrors;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {        
        $client_segment = ClientSegment::where("name",$row["iva"])->first();
   
        $PROVINCIAS = File::json(base_path('public/JSON/provincias.json'));
        $DEPARTAMENTOS = File::json(base_path('public/JSON/departamentos.json'));
        $LOCALIDADES = File::json(base_path('public/JSON/localidades.json'));

        $PROVINCIA_SELECTED = null;
        $DEPARTAMENTO_SELECTED = null;
        $LOCALIDAD_SELECTED = null;
        foreach ($PROVINCIAS as $key => $PROVINCIA) {
            if(Str::upper($PROVINCIA["name"]) == Str::upper(trim($row["provincia"]))){
                $PROVINCIA_SELECTED = $PROVINCIA;
                break;
            }
        }

        foreach ($DEPARTAMENTOS as $key => $DEPARTAMENTO) {
            if($DEPARTAMENTO["provincia_id"] == $PROVINCIA_SELECTED["id"] && 
                Str::upper($DEPARTAMENTO["name"]) == Str::upper(trim($row["departamento"]))){
                $DEPARTAMENTO_SELECTED = $DEPARTAMENTO;
                break;
            }
        }

        foreach ($LOCALIDADES as $key => $LOCALIDAD) {
            if($LOCALIDAD["departamento_id"] == $DEPARTAMENTO_SELECTED["id"] && 
                Str::upper($LOCALIDAD["name"]) == Str::upper(trim($row["localidad"]))){
                $LOCALIDAD_SELECTED = $LOCALIDAD;
                break;
            }
        }        

        return new Client([            
            "code" => $row["codigo"] ? $row["codigo"] : NULL,
            "surname" => $row["apellido"],
            "name" => $row["nombre"],
            "razon_social" => $row["razon_social"],
            "client_segment_id" => $client_segment->id,
            "phone" => $row["telefono"],
            "celular" => $row["celular"],
            "email" => $row["correo"] ? $row["correo"] : NULL,
            "type_document" => $row["tipo_dni"],
            "n_document" => $row["dni"] ? $row["dni"] : NULL,
            "cuit" => $row["cuit"] ? $row["cuit"] : NULL,
            "address" => $row["direccion"],      
            "state" => $row["estado"] == 'Activo' ? 1 : 2,
            "ubigeo_provincia" => $PROVINCIA_SELECTED ?  $PROVINCIA_SELECTED["id"] : NULL,
            "ubigeo_departamento" => $DEPARTAMENTO_SELECTED ?  $DEPARTAMENTO_SELECTED["id"] : NULL,
            "ubigeo_localidad" => $LOCALIDAD_SELECTED ?  $LOCALIDAD_SELECTED["id"] : NULL,
            "provincia" => $row["provincia"],
            "departamento" => $row["departamento"],
            "localidad" => $row["localidad"]
        ]);
    }

    public function rules(): array
    {
        return [            
            // "*.correo" => ['nullable'],
            // "*.client_segment_id" => ['required'],
            // "*.provincia" => ['required'],
            // "*.departamento" => ['required'],
            // "*.localidad" => ['required'],
            // "*.state" => ['required']
        ];
    }
}
