<?php

namespace App\Imports;

use App\Models\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\User;
use Illuminate\Validation\ValidationException;
use App\Models\OrganizationRole;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ProductTypeImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function __construct($organId)
    {
        $this->organId = $organId;        
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required'
            ]
        ];
    }

    public function customValidationMessages()
    {
        return [
            // 'no_kp.unique' => 'Terdapat maklumat guru yang telah wujud',
            'nama.required' => 'Nama perlu diisikan',
            
        ];
    }

    public function model(array $row)
    {
        if(!isset($row['nama'])){
            throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        }

        // check if parent role exists
        $ifExits = DB::table('product_group as pg')
                   ->where('pg.organization_id','=',$this->organId)
                    // ->where('u.icno', '=', "{$row['no_kp']}")
                    ->where('pg.name', '=', "{$row['nama']}")
                    ->get();
        
        if(count($ifExits) == 0) // if not product type with same name
        {
            $newProductType = new ProductGroup([
                //
                'name'      => $row['nama'],
                'created_at'     => now(),
                'updated_at'     => now(),
                'organization_id' => $this->organId,
    
            ]);
    
            $newProductType->save();
        }          
        else
        {
            throw ValidationException::withMessages(["error" => "Duplication of product type name"]);
        }

    
    }


}
