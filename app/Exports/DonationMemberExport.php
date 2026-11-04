<?php

namespace App\Exports;

use App\Http\Controllers\PointController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DonationMemberExport implements FromCollection, WithHeadings
{
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return app(PointController::class)->getReferralCodeMemberData($this->userId)
            ->select(
                'mu.name as member_name',
                'mu.telno as member_telno',
                'mu.email as member_email',
                'rcm.created_at',
                DB::raw('COALESCE(ph_total.total_points, 0) as contribution'),
                DB::raw('COALESCE(ph_today.today_points, 0) as todayContribution')
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Ahli',
            'No Telefon',
            'Email',
            'Tarikh Masuk',
            'Jumlah Sedekah',
            'Sedekah Hari Ini'
        ];
    }
}
