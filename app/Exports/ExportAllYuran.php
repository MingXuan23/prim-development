<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportAllYuran implements FromCollection, WithHeadings
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
            ->orderBy('category')
            ->orderBy('name')
            ->select('id', 'name', 'status')
            ->get();
    }

    public function collection()
    {
        // get all students and student_fees_new
        $studentsByOrg = DB::table('students as s')
            ->join('class_student as cs', 'cs.student_id', '=', 's.id')
            ->join('student_fees_new as sfn', 'sfn.class_student_id', '=', 'cs.id')
            ->join('class_organization as co', 'co.id', '=', 'cs.organclass_id')
            ->join('classes as c', 'c.id', '=', 'co.class_id')
            ->where('co.organization_id', '=', $this->orgId)
            ->get();

        return collect([]);
    }

    public function headings(): array
    {
        return array_merge([
            "nama",
            "kelas"
        ], $this->feesByOrg->pluck('name')->toArray());
    }
}
