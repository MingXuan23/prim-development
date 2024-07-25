<?php

namespace App\Exports;
use stdClass;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Symfony\Component\VarDumper\Cloner\Data;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportKoperasiOverview implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct($orgId)
    {
        $this->orgId = $orgId;
        
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=[];
        //array_push($data, $feedata);
        
        $items = DB::table('product_item as pi')
        ->join('product_group as g','g.id','pi.product_group_id')
        ->where('g.organization_id',$this->orgId)
        ->select('pi.id','pi.name')
        ->get();

        $no=1;
        foreach($items as $i){
            $details = new stdClass();
            $item = DB::table('pgng_orders as pg')
            ->join('product_order as po', 'pg.id', '=', 'po.pgng_order_id')
            ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
            ->join('organizations as o', 'pg.organization_id', '=', 'o.id')
            ->join('transactions as t', 't.id', '=', 'pg.transaction_id')
            ->where('pi.id',$i->id)
            ->where('pg.status', '>', 1)
            ->where('t.status','Success')
            ->select( 'po.quantity as quantity','pg.status as status','t.id')
            ->get();
            
            if(count($item)==0)
                continue;
            $details->no = $no++; 
            $details->name = $i->name;
            $details->pending = $item->whereIn('status',[2,4])->sum('quantity');
            
            // Filter items with status 3
            $details->completed =  $item->where('status',3)->sum('quantity');
            $details->total =$item->sum('quantity');
            array_push($data, $details);
            
            
            
        }
        $details = new stdClass();

        $details->no = "Total Income";
        
        $income = DB::table('pgng_orders as pg')
        ->join('product_order as po', 'pg.id', '=', 'po.pgng_order_id')
        ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
        ->join('organizations as o', 'pg.organization_id', '=', 'o.id')
        ->join('transactions as t', 't.id', '=', 'pg.transaction_id')
        ->where('pg.organization_id', $this->orgId)
        ->where('pg.status', '>', 1)
        ->where('t.status','Success')
        ->distinct('pg.id') 
        ->select('pg.id','pg.total_price','pg.status')
        ->get(); // Sum the total column
        $details->income =0;

        foreach($income as $i){
            $details->income += $i->total_price;
        }

        
        array_push($data, $details);
       
        
        
        //$data=
        return collect($data);
    }

    public function headings(): array
    {

        return [
            'No',
            'Nama Barang',
            'Quantiti Belum Selesai',
            'Quantiti Selesai',
            'Jumlah Quantiti dijual',

            
        ];
    }
}
