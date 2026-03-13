<?php

namespace App\Imports;

use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClassImport implements ToModel, WithHeadingRow
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
        if (!isset($row['nama_kelas']) || !isset($row['tahap_kelas'])) {
            throw ValidationException::withMessages(["error" => "Invalid headers or missing column"]);
        }

        $classExists = DB::table('classes as c')
            ->join('class_organization as co', 'co.class_id', '=', 'c.id')
            ->where('nama', '=', $row['nama_kelas'])
            ->where('levelid', '=', $row['tahap_kelas'])
            ->where('co.organization_id', '=', $this->organId)
            ->exists();

        if ($classExists) {
            throw ValidationException::withMessages(["error" => "Kelas {$row['nama_kelas']}, tahap {$row['tahap_kelas']} sudah ditambah sebelum ini."]);
        }

        // dd($this->organId);
        $newclass = new ClassModel([
            //
            'nama' => $row['nama_kelas'],
            'levelid' => $row['tahap_kelas'],
            'status' => 1,
        ]);

        $newclass->save();

        DB::table('class_organization')->insert([
            'organization_id' => $this->organId,
            'class_id' => $newclass->id,
            'start_date' => now(),
        ]);
    }
}
