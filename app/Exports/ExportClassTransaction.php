<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use stdClass;

class ExportClassTransaction implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    // For use in the AfterSheet event (static because the event callback is static)
    private static $organizationName;
    private static $startDate;
    private static $endDate;
    private static $showAllPayment;

    protected $kelas;
    protected $org;
    protected $start_date;
    protected $end_date;
    protected $show_all_payment;

    public function __construct($kelas, $org, $start_date, $end_date, $show_all_payment)
    {
        $this->kelas            = $kelas;
        $this->org              = $org;
        $this->start_date       = $start_date;
        $this->end_date         = $end_date;
        $this->show_all_payment = $show_all_payment;
        
        // Save extra info for use in the event
        self::$organizationName = $org->nama;
        self::$startDate        = $start_date;
        self::$endDate          = $end_date;
        self::$showAllPayment   = $show_all_payment ? 'Ya' : 'Tidak';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        set_time_limit(300);
        $results = [];
        $datas = $this->getData(); // get data of all students and parents

        foreach ($datas as $data) {

            $tranA = DB::table('transactions as t')
                ->leftJoin('fees_new_organization_user as fou', 't.id', 'fou.transaction_id')
                ->leftJoin('fees_new as fn', 'fn.id', 'fou.fees_new_id')
                ->distinct()
                ->where('t.user_id', $data->userId)
                ->where('t.status', 'Success')
                ->whereBetween('t.datetime_created', [$data->start_date, $this->end_date])
                ->where('fn.organization_id', $this->org->id)
                ->select('t.*', 'fn.name as yuran', 'fn.totalAmount as yuranAmount')
                ->get();

            $tranBC = DB::table('transactions as t')
                ->leftJoin('fees_transactions_new as ftn', 't.id', 'ftn.transactions_id')
                ->leftJoin('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                ->leftJoin('fees_new as fn', 'fn.id', 'sfn.fees_id')
                ->distinct()
                ->where('t.user_id', $data->userId)
                ->where('fn.organization_id', $this->org->id)
                ->where('t.status', 'Success')
                ->whereBetween('t.datetime_created', [$data->start_date, $this->end_date])
                ->select('t.*', 'fn.name as yuran', 'fn.totalAmount as yuranAmount')
                ->get();

            $combined = $tranA->concat($tranBC);
            $unique   = $combined->unique('id');

            $amount = 0.00;
            // Concatenate transaction dates (customize formatting as needed)
            $transaction_date = implode(',', $unique->pluck('datetime_created')->all());

            foreach ($combined as $tran) {
                $amount += $tran->yuranAmount;
            }

            $temp = new stdClass();
            $temp->organization_name = self::$organizationName;
            $temp->nama              = $data->nama;
            $temp->nama_kelas        = $data->nama_kelas;
            $temp->date              = $transaction_date ?: '';
            $temp->amount            = $amount == 0.00 ? 'RM 0.00' : 'RM ' . number_format($amount, 2, '.', '');

            $results[] = $temp;
        }
        return collect($results);
    }

    public function getData()
    {
        $this_start_date = $this->start_date;
        $this_end_date   = $this->end_date;

        if ($this->kelas != 0) {
            $datas = DB::table('students as s')
                ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id')
                ->leftJoin('users as u', 'u.id', 'ou.user_id')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->leftJoin('classes as c', 'c.id', 'co.class_id')
                ->where('c.id', $this->kelas)
                // ->where(function ($query) use ($this_start_date, $this_end_date) {
                //     $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                //           ->orWhere(function ($query) use ($this_end_date) {
                //               $query->whereNull('cs.end_date')
                //                     ->where('cs.start_date', '<=', $this_end_date);
                //           });
                // })
                ->where(function($query) use ($this_start_date, $this_end_date) {
                    $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                          ->orWhere(function($query) use ($this_end_date) {
                              $query->whereNull('cs.end_date')
                                    ->where('cs.start_date', '<=', $this_end_date);
                          })
                          ->orWhere(function($query) use ($this_start_date, $this_end_date) {
                            $query->whereNotNull('cs.end_date')
                                  ->whereBetween('cs.end_date',  [$this_start_date, $this_end_date]);
                        })
                        ->orWhere(function($query) use ($this_start_date, $this_end_date) {
                            $query->whereNotNull('cs.end_date')
                                  ->where( 'cs.end_date','>=', $this_start_date)
                                  ->where('cs.start_date','<=',$this_end_date);

                        });
                        
                }) 

                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid', 'cs.start_date')
                ->orderBy('s.nama')
                ->get();
        } else {
            if ($this->org->type_org == 10) {
                $datas = DB::table('students as s')
                    ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                    ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id')
                    ->leftJoin('users as u', 'u.id', 'ou.user_id')
                    ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                    ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->leftJoin('classes as c', 'c.id', 'co.class_id')
                    ->where('co.organization_id', $this->org->parent_org)
                    ->where(function($query) use ($this_start_date, $this_end_date) {
                        $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                              ->orWhere(function($query) use ($this_end_date) {
                                  $query->whereNull('cs.end_date')
                                        ->where('cs.start_date', '<=', $this_end_date);
                              })
                              ->orWhere(function($query) use ($this_start_date, $this_end_date) {
                                $query->whereNotNull('cs.end_date')
                                      ->whereBetween('cs.end_date',  [$this_start_date, $this_end_date]);
                            })
                            ->orWhere(function($query) use ($this_start_date, $this_end_date) {
                                $query->whereNotNull('cs.end_date')
                                      ->where( 'cs.end_date','>=', $this_start_date)
                                      ->where('cs.start_date','<=',$this_end_date);
    
                            });
                            
                    }) 
                    ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid', 'cs.start_date')
                    ->orderBy('c.nama')
                    ->orderBy('s.nama')
                    ->get();
            } else {
                $datas = DB::table('students as s')
                    ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                    ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id')
                    ->leftJoin('users as u', 'u.id', 'ou.user_id')
                    ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                    ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                    ->leftJoin('classes as c', 'c.id', 'co.class_id')
                    ->where('co.organization_id', $this->org->id)
                    ->where(function($query) use ($this_start_date, $this_end_date) {
                        $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                              ->orWhere(function($query) use ($this_end_date) {
                                  $query->whereNull('cs.end_date')
                                        ->where('cs.start_date', '<=', $this_end_date);
                              })
                              ->orWhere(function($query) use ($this_start_date, $this_end_date) {
                                $query->whereNotNull('cs.end_date')
                                      ->whereBetween('cs.end_date',  [$this_start_date, $this_end_date]);
                            })
                            ->orWhere(function($query) use ($this_start_date, $this_end_date) {
                                $query->whereNotNull('cs.end_date')
                                      ->where( 'cs.end_date','>=', $this_start_date)
                                      ->where('cs.start_date','<=',$this_end_date);
    
                            });
                            
                    }) 
                    ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid', 'cs.start_date')
                    ->orderBy('c.nama')
                    ->orderBy('s.nama')
                    ->get();
            }
        }
        return $datas;
    }

    /**
     * Define the column headings for the export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Sekolah',
            'Nama Pelajar',
            'Nama Kelas',
            'Tarikh Pembayaran',
            'Jumlah Pembayaran Yuran'
        ];
    }

    /**
     * Register events to add extra header rows with extra info.
     *
     * @return array
     */
    public  function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Insert 3 new rows at the top
                $sheet->insertNewRowBefore(1, 3);

                // Row 1: Main title
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'Laporan Transaksi Kelas');

                // Row 2: Organization name and date range
                $sheet->mergeCells('A2:C2');
                $sheet->setCellValue('A2', 'Nama Sekolah: ' . self::$organizationName);
                $sheet->mergeCells('D2:E2');
                $sheet->setCellValue('D2', 'Tarikh: ' . self::$startDate . ' - ' . self::$endDate);

                // Row 3: Show All Payment info
                $sheet->mergeCells('A3:E3');
                $sheet->setCellValue('A3', 'Tunjuk Semua Bayaran: ' . self::$showAllPayment);

                // Optionally, style header rows
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $sheet->getStyle('A1')->applyFromArray($headerStyle);
                $sheet->getStyle('A2:E2')->applyFromArray($headerStyle);
                $sheet->getStyle('A3')->applyFromArray($headerStyle);
            }
        ];
    }
}
