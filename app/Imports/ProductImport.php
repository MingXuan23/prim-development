<?php

namespace App\Imports;

use App\Models\ProductGroup;
use App\Models\ProductItem;
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

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function __construct($groupId,$target,$organ)
    {
        $this->groupId = $groupId;
        $this->target=$target;   
        $this->organId=$organ;
              
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required'
            ],
            'quantity' => [
                'required'
            ],
            'price' => [
                'required'
            ],

        ];
    }

    public function customValidationMessages()
    {
        return [
            // 'no_kp.unique' => 'Terdapat maklumat guru yang telah wujud',
            'name.required' => 'Nama perlu diisikan',
            'quantity.required'=>'Quantiti perlu diisikan',
            'price.required'=>"Harga perlu diisikan"
            
        ];
    }

    public function model(array $row)
    {
        if(!isset($row['name'])||!isset($row['quantity'])||!isset($row['price'])){
            throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        }
        if(!is_int($row['quantity'])||is_float($row['price'])){
            throw ValidationException::withMessages(["error" => "Invalid format of quantity or price"]);
        }

        // check if parent role exists
        $ifExits = DB::table('product_item as p')
                    ->leftJoin('product_group as pg','pg.id','=','p.product_group_id')
                   ->where('pg.organization_id','=',$this->organId)
                    // ->where('u.icno', '=', "{$row['no_kp']}")
                    ->where('p.name', '=', "{$row['name']}")
                    ->whereNull('pg.deleted_at')
                    ->get();
        
        if(count($ifExits) == 0) // if not product type with same name
        { 
            $newProduct = new ProductItem([
                //
                'name'      => $row['name'],
                'quantity_available'=>$row['quantity'],
                'price' => $row['price'],
                'created_at'     => now(),
                'updated_at'     => now(),
                'target'         =>$this->target,
                'product_group_id' => $this->groupId,
                'status'           =>1,
            ]);
            $newProduct->save();
        }          
        else
        {
            throw ValidationException::withMessages(["error" => "Duplication of product name"]);
        }

    
    }


}
