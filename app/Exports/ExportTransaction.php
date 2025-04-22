<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use stdClass;

class ExportTransaction implements FromCollection, ShouldAutoSize, WithHeadings
{
    // For use in the AfterSheet event (static because the event callback is static)
    private static $organizationName;
  

    
    protected $org;
    protected $list;
  
    

    public function __construct($org, $list)
    {
      
        $this->org              = $org;
        $this->list       = $list;
      
      
        
        // Save extra info for use in the event
        self::$organizationName = $org->nama;
        
      
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        set_time_limit(300);
        $results = [];
        $datas = $this->list;

        foreach ($datas as $data) {

            $link = route('receipttest', $data->id);
           
            

            $temp = new stdClass();
           
            $temp->name              = $data->name;
            
            $temp->fpx_id           = "'".$data->transac_no;

            $temp->date              = $data->date ;
            $temp->amount            = 'RM ' . number_format($data->amount, 2, '.', '');
            $temp->username         = $data->username;
            $temp->link             =$link;


            $results[] = $temp;
        }
        return collect($results);
    }

   

    /**
     * Define the column headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'No Receipt',
            'FPX Id',
            'Tarikh Pembayaran',
            'Jumlah Pembayaran',
            'Nama Pembayar',
            "Receipt"
        ];
    }

   
}
