<?php

namespace App\Exports;

use App\models\Participant;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ParticipantExport implements FromQuery, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $request;
    private $no = 1;

    public function __construct($request)
    {
        $this->request = $request;
    }
    
    public function startCell(): string
    {
        return 'A3';
    }

    public function registerEvents(): array
    {
        return [
          AfterSheet::class => function(AfterSheet $event) {
            $title = $this->request->competitionTitle ?? 'SENARAI PENDAFTARAN PERTANDINGAN: ';
            $event->sheet->setCellValue('A1', strtoupper($title));
            $event->sheet->mergeCells('A1:E1');

            $sheet = $event->sheet->getDelegate();
            foreach(range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
          }  
        ];
    }

    public function query()
    {
        $userId = Auth::id();
        $query = Participant::whereHas('team.team_registration', function($q) use ($userId) 
        {
            $q->where('userId', $userId);
        })->with(['user', 'team.team_registration']);

        if ($this->request->filled('title') && $this->request->title !== 'all')
        {
            $title = $this->request->title;
            $query->whereHas('team.team_registration', function ($q) use ($title) {
                $q->where('competitionTitle', $title);
            });
        }

        if ($this->request->filled('search'))
        {
            $cari = trim($this->request->search);
            $query->where(function($q) use ($cari)
            {
                $q->where('name', 'like', "%{$cari}%");
            });
        }
        
        if ($this->request->filled('status') && $this->request->status !== 'all')
        {
            $cari = $this->request->status;
            $query->whereHas('team.team_registration', function($q) use ($cari)
            {
                $q->where('statusPayment', ucfirst($cari));
            });
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Peserta',
            'No IC / No Pasport',
            'Email',
            'No Telefon',
            'Nama Institusi',
            'Pertandingan',
            'Status Pembayaran',
            'Nama Kumpulan',
        ];
    }

    public function map($participant): array
    {
        $competition = $participant->team->team_registration->first();
        $participateType = $competition->participateType === 'Team';
        $groupName = $participateType ? ($participant->isLeader === 'Leader' ? $participant->team->groupName . ' - (K)' : $participant->team->groupName) : '-';

        return [
            $this->no++,
            $participant->name,
            $participant->icNo ?? 'N/A',
            $participant->email,
            $participant->noTel,
            $participant->institution->instituteName,
            $competition->competitionTitle,
            $competition->pivot->statusPayment,
            $groupName,
        ];
    }
}
