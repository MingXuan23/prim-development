<?php

namespace App\Exports;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WardenExport implements FromCollection, ShouldAutoSize, WithHeadings
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

        // dd($this->organId);
        $listteachers = DB::table('users')
            ->join('organization_user', 'organization_user.user_id', '=', 'users.id')
            ->select('users.name', 'users.email', 'users.telno')
            ->where([
                ['organization_user.organization_id', $this->organId],
                ['organization_user.role_id', 7]
            ])
            ->orderBy('users.name')
            ->get();

        foreach ($listteachers as $listteacher) {
            $listteacher->telno = str_replace('+6', '', $listteacher->telno);
        }

        return $listteachers;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'email',
            'No. Tel Bimbit'
        ];
    }
}
