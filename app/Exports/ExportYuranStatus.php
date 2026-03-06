<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Symfony\Component\VarDumper\Cloner\Data;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use Maatwebsite\Excel\Concerns\Exportable;

class ExportYuranStatus implements WithMultipleSheets
{
    private $yuran;
    private $feeYear;
    private $includeMasihBerhutang;

    public function __construct($yuran, $feeYear, $includeMasihBerhutang = true)
    {
        $this->yuran = $yuran;
        $this->feeYear = $feeYear;
        $this->includeMasihBerhutang = $includeMasihBerhutang;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

    public function fetchdata($yuran)
    {
        if ($yuran->category == "Kategori A") {

            $org = DB::table('organizations as o')
                ->where('o.id', $yuran->organization_id)
                ->first();

            $orgId = $org->id;
            if ($org->parent_org != null) {
                $orgId = $org->parent_org;
            }

            $data = DB::table('organization_user as ou')
                ->join('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
                ->join('students as s', 's.id', 'ous.student_id')
                ->join('class_student as cs', 'cs.student_id', 's.id')
                ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                ->join('classes as c', 'c.id', 'co.class_id')
                ->join('fees_new_organization_user as fou', 'fou.organization_user_id', '=', 'ou.id')
                ->join('fees_new as fn', 'fn.id', 'fou.fees_new_id')
                ->leftJoin('transactions as t', 't.id', 'fou.transaction_id')
                ->where('ou.organization_id', $orgId)
                ->where('fou.fees_new_id', $yuran->id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('cs.start_date', '<=', $this->feeYear . '-12-31')
                            ->where('cs.end_date', '>=', $this->feeYear . '-01-01');
                    })
                        ->orWhere(function ($query) {
                            $query->where('cs.start_date', '<=', $this->feeYear . '-12-31')
                                ->whereNull('cs.end_date');
                        });
                })
                ->when(!$this->includeMasihBerhutang, function ($query) {
                    $query->where('fou.status', 'Paid');
                })
                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'fou.status', 'fn.totalAmount')
                ->orderByDesc('cs.id')
                ->orderByDesc('t.status')
                ->get();

        } else {
            $data = DB::table('students as s')
                ->join('class_student as cs', 'cs.student_id', 's.id')
                ->join('class_organization as co', 'co.id', 'cs.organclass_id')
                ->join('classes as c', 'c.id', 'co.class_id')
                ->join('student_fees_new as sfn', 'sfn.class_student_id', 'cs.id')
                ->join('fees_new as fn', 'fn.id', 'sfn.fees_id')
                ->leftJoin('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                ->leftJoin('transactions as t', 'ftn.transactions_id', 't.id')
                ->where('sfn.fees_id', $yuran->id)
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('cs.start_date', '<=', $this->feeYear . '-12-31')
                            ->where('cs.end_date', '>=', $this->feeYear . '-01-01');
                    })
                        ->orWhere(function ($query) {
                            $query->where('cs.start_date', '<=', $this->feeYear . '-12-31')
                                ->whereNull('cs.end_date');
                        });
                })
                ->when(!$this->includeMasihBerhutang, function ($query) {
                    $query->where('sfn.status', 'Paid');
                })
                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'sfn.status', 'fn.totalAmount')
                ->orderByDesc('cs.id')
                ->orderByDesc('t.status')
                ->get();
        }

        // sort based on class, student name
        $data = $data
            ->unique('nama')
            ->sortBy(function ($d) {
                return [
                    $d->nama_kelas,
                    $d->nama,
                ];
            })
            ->values();

        if ($this->includeMasihBerhutang) {
            $data = $data->map(function ($item) {
                if ($item->status == 'Debt') {
                    $item->totalAmount = null;
                }

                return $item;
            });
        }

        foreach ($data as $student) {
            $student->status = $student->status == "Debt" ? "Masih Berhutang" : "Telah Bayar";
        }

        return $data;
    }

    // public function collection()
    // {   
    //     $sheets = [];
    //     $heading =[
    //         'Nama',
    //         'Kelas',
    //         'Jantina',
    //         'Status',
    //     ];
    //     foreach( $this->yuran as $y){
    //         $datas = $this->fetchdata($y);
    //         $sheets[] = new Sheet($datas, $heading);
    //     }
    //     return $sheets;

    // }

    public function sheets(): array
    {
        $sheets = [];
        $heading = [
            'Nama',
            'Kelas',
            'Jantina',
            'Status',
            'Jumlah Bayaran'
        ];

        // foreach ($this->yuran as $yuranItem) {
        //     $data = $this->fetchdata($yuranItem);

        //     $yuranName = mb_substr($yuranItem->name, 0, 10) .
        //         "..." .
        //         mb_substr($yuranItem->name, mb_strlen($yuranItem->name) - 10, -10);

        //     $sheets[] = new Sheet($data, $heading, $yuranName . ($yuranItem->status == 1 ? ' (Active)' : ' (Inactive)'));

        // }

        foreach ($this->yuran as $yuranItem) {
            $data = $this->fetchdata($yuranItem);

            $sheets[] = new Sheet($data, $heading, $yuranItem->name . ($yuranItem->status == 1 ? ' (Aktif)' : ' (Tidak Aktif)'));

        }

        return $sheets;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas',
            'Jantina',
            'Status',
        ];
    }
}
