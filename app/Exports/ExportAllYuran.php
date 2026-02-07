<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAllYuran implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $orgId;
    private $feesByOrg;
    private $year;

    public function __construct($orgId, $year)
    {
        $this->orgId = $orgId;
        $this->year = $year;

        // get all fees by organization
        $this->feesByOrg = DB::table("fees_new")
            ->where('organization_id', '=', $this->orgId)
            ->where('start_date', '<=', $this->year . "-12-31")
            ->where('end_date', '>=', $this->year . '-01-01')
            ->orderBy('category', 'asc')
            ->orderByDesc('status')
            ->orderBy('name', 'asc')
            ->select('id', 'name', 'status', 'category')
            ->get();
    }

    public function collection()
    {
        set_time_limit(300);

        // get all students
        $studentsByOrg = DB::table('students as s')
            ->join('class_student as cs', 'cs.student_id', '=', 's.id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('classes as c', 'c.id', '=', 'co.class_id')
            ->where('co.organization_id', '=', $this->orgId)
            ->where(function ($query) {
                if ($this->year == now()->year) {
                    $query->where('cs.start_date', '<=', $this->year . '-12-31')
                        ->whereNull('cs.end_date');
                } else {
                    $query->where('cs.start_date', '<=', $this->year . '-12-31')
                        ->where('cs.end_date', '>=', $this->year . '-01-01');
                }
            })
            ->select('s.id as student_id', 's.nama as student_name', 'c.nama as nama_kelas', 'cs.id as class_student_id')
            ->orderBy('c.nama', 'asc')
            ->orderBy('s.nama', 'asc')
            ->get();

        // get all fees_new_organization_user (for Kategori A)
        $feesNewOrganizationUsers = DB::table("fees_new_organization_user as fou")
            ->join('organization_user as ou', 'ou.id', '=', 'fou.organization_user_id')
            ->join('organization_user_student as ous', 'ou.id', '=', 'ous.organization_user_id')
            ->whereIn('ous.student_id', $studentsByOrg->pluck('student_id'))
            ->select('ou.id as organization_user_id', 'ous.student_id as student_id', 'fou.fees_new_id as fees_id', 'fou.status')
            ->get()
            ->groupBy(function ($row) {
                // using indexing
                return $row->student_id . '_' . $row->fees_id;
            });

        // get all student_fees_new with transactions (for Kategori B and Kategori C)
        $studentFeesNew = DB::table("student_fees_new")
            ->whereIn('class_student_id', $studentsByOrg->pluck('class_student_id'))
            ->get()
            ->groupBy(function ($row) {
                // using indexing
                return $row->class_student_id . '_' . $row->fees_id;
            });

        // store completed results after filtering and attaching fees to students
        $results = collect([]);

        // attach fees status to each student
        // group students by student_id in case some of them have more than one class_student
        foreach ($studentsByOrg->groupBy('student_id') as $studentId => $studentClasses) {

            $studentCurrentClass = $studentClasses->sortByDesc('class_student_id')->values()->first();

            $row = (object) [
                'student_name' => $studentCurrentClass->student_name,
                'nama_kelas' => $studentCurrentClass->nama_kelas
            ];

            foreach ($this->feesByOrg as $fee) {
                if ($fee->category == "Kategori A") {
                    // key is the index in feesNewOrganizationUsers
                    $key = $studentId . '_' . $fee->id;

                    // search the fees_new_organization_user using the key to improve performance
                    $feeStatus = $feesNewOrganizationUsers[$key][0]->status ?? null;
                } else {
                    // key is the index in studentFeesNew
                    $key = $studentCurrentClass->class_student_id . '_' . $fee->id;

                    // search the student_fees_new using the key to improve performance
                    $feeStatus = $studentFeesNew[$key][0]->status ?? null;
                }

                if ($feeStatus == null) {
                    $row->{$fee->name} = '-';
                } else if ($feeStatus == 'Debt') {
                    $row->{$fee->name} = 'Masih Berhutang';
                } else {
                    $row->{$fee->name} = 'Telah Bayar';
                }
            }

            $results->add($row);
        }

        // resort the collection (order might change due to more than one class_student)
        $results = $results->sortBy(function ($item) {
            return [$item->nama_kelas, $item->student_name];
        })->values();

        return $results;
    }

    public function headings(): array
    {
        return array_merge([
            "nama",
            "kelas"
        ], $this->feesByOrg->pluck('name')->toArray());
    }
}
