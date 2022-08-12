<?php

namespace App\Imports;

use App\Models\Dorm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DormImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function __construct($organId)
    {
        $this->organId = $organId;
    }

    public function model(array $row)
    {
        if (!isset($row['nama_asrama']) || !isset($row['kapasiti']) || !isset($row['bilangan_pelajar_dalam'])) {
            throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        }

        // dd($this->organId);
        $newdorm = new Dorm([
            //
            'nama'      => $row['nama_asrama'],
            'accommodate_no'   => $row['kapasiti'],
            'student_inside_no'    => 0,
            'organization_id' => $this->organId,
            //'name', 'accommodate_no', 'student_inside_no'
        ]);

        $newdorm->save();
    }
}
