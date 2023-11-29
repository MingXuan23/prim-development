<?php
namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class SubjectImport implements ToCollection, WithBatchInserts
{
    protected $organization;

    public function __construct($organization)
    {
        $this->organization = $organization;
    }

    public function collection(Collection $rows)
    {
        // Skip the first row (headers)
        $rows->shift();
        foreach ($rows as $row) {
            // Check if the subject or code already exists
            $subject_name=trim(strtoupper($row[0]));
            $code=trim(strtoupper($row[1]));
            $isExists = DB::table('subject as s')
                ->where('s.organization_id', $this->organization)
                ->where(function ($query) use ($subject_name,$code) {
                    $query->where('s.name', '=',$subject_name) // Assuming the name is in the first column
                        ->orWhere('s.code', '=', $code); // Assuming the code is in the second column
                })
                ->exists();

            if (!$isExists) {
                // Insert the new subject
                DB::table('subject')->insert([
                    'name' =>$subject_name, // Assuming the name is in the first column
                    'code' =>$code, // Assuming the code is in the second column
                    'organization_id' => $this->organization,
                ]);
            }
        }
    }

    public function batchSize(): int
    {
        return 1000; // Adjust the batch size as needed
    }
}
?>