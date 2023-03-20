<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportJumlahBayaranIbuBapa implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct($kelas)
    {
        $this->kelas = $kelas;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        $datas = DB::table('students as s')
            ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
            ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
            ->leftJoin('users as u', 'u.id', 'ou.user_id')
            ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
            ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
            ->leftJoin('classes as c', 'c.id', 'co.class_id')
            ->where('c.id', $this->kelas->id)
            ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid')
            ->orderBy('s.nama')
            ->get();
        
        foreach ($datas as $key => $data) {

            $totalTran = DB::table('transactions as t')
                ->leftJoin('fees_new_organization_user as fou', 't.id', 'fou.transaction_id')
                ->leftJoin('fees_transactions_new as ftn', 't.id', 'ftn.transactions_id')
                ->distinct()
                ->where('t.user_id', $data->userId)
                ->where('t.status', 'Success')
                ->sum('t.amount');

            $data->totalAmount = $totalTran == 0 ? 'RM 0.00' : 'RM ' . number_format($totalTran, 2, '.', '');

            unset($data->userId);
            unset($data->sid);
            unset($data->oid);
        }

        return $datas;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas',
            'Jantina',
            'Nama Penjaga',
            'Jumlah Pembayaran Penjaga'
        ];
    }
}
