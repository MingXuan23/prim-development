<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FormResponsesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        set_time_limit(300);

        // get all form_responses
        $formResponses = DB::table("form_responses")->get();

        $dataToExport = [];

        foreach ($formResponses as $response) {
            $jsonData = json_decode($response->response);

            $data = [
                'nama_yuran' => $jsonData->fee_name,
                'penerangan' => $jsonData->desc,
                'kuantiti' => $jsonData->quantity,
                'harga' => $jsonData->price,
                'jumlah' => $jsonData->total_amount,
                'tujuan' => $response->purpose,
                'nama_penjaga' => $jsonData->penjaga_name,
                'nama_pelajar' => $jsonData->student_name,
                'saiz_baju' => $jsonData->shirt_size,
            ];

            $dataToExport[] = $data;
        }

        return collect($dataToExport);
    }

    public function headings(): array
    {
        return [
            'nama_yuran',
            'penerangan',
            'kuantiti',
            'harga',
            'jumlah',
            'tujuan',
            'nama_penjaga',
            'nama_pelajar',
            'saiz_baju',
        ];
    }
}
