<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Sheet implements FromCollection, WithHeadings,WithTitle
{
    protected $data;
    protected $headings;
    protected $title;


    public function __construct(Collection $data, array $headings ,$title)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
    }

    public function collection()
    {
        
        return $this->data;
    }

    public function headings(): array
    {
        return [
            [$this->title],
            $this->headings
            
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}
