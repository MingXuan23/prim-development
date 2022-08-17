<?php

namespace App\Exports;

use App\Models\Outing;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OutingExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    
    public function __construct($organId)
    {
        $this->organId = $organId;
    }

    public function collection()
    {
        $outinglist = DB::table('outings')
            ->select('outings.start_date_time', 'outings.end_date_time')
            ->where([
                ['outings.organization_id', $this->organId],
            ])
            ->orderBy('outings.start_date_time')
            ->get();

        return $outinglist;
    }

    public function headings(): array
    {
        return [
            'Tarikh dan Masa Keluar',
            'Tarikh dan Masa Masuk'
        ];
    }
}
