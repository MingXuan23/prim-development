<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportJumlahBayaranIbuBapa implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct($kelas, $org,$start_date,$end_date)
    {
        $this->kelas = $kelas;
        $this->org=$org;
        $this->oid = $org->id;
        $this->start_date = $start_date;
        $this->end_date= $end_date;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //comment only
        set_time_limit(300);
        $datas=$this->getData();//get data of all students and parents
        
        foreach ($datas as $key => $data) {

            $tranA = DB::table('transactions as t')
                ->leftJoin('fees_new_organization_user as fou', 't.id', 'fou.transaction_id')
                ->leftJoin('fees_new as fn', 'fn.id', 'fou.fees_new_id')
                ->distinct()
                ->where('t.user_id', $data->userId)
                ->where('t.status', 'Success')
                //->where('t.datetime_created','>=',$data->start_date)
                ->whereBetween('t.datetime_created', [$this->start_date, $this->end_date])

                ->where('fn.organization_id', $this->oid)
                ->select('t.*','fn.name as yuran','fn.totalAmount as yuranAmount')
                ->get();

                
            $tranBC = DB::table('transactions as t')
                ->leftJoin('fees_transactions_new as ftn', 't.id', 'ftn.transactions_id')
                ->leftJoin('student_fees_new as sfn', 'sfn.id', 'ftn.student_fees_id')
                ->leftJoin('fees_new as fn', 'fn.id', 'sfn.fees_id')
                ->distinct()
                ->where('t.user_id', $data->userId)
                ->where('fn.organization_id', $this->oid)
                ->where('t.status', 'Success')
                ->whereBetween('t.datetime_created', [$this->start_date, $this->end_date])
                ->select('t.*','fn.name as yuran','fn.totalAmount as yuranAmount')
                ->get();        
                
            $combined = $tranA->concat($tranBC);

            $unique = $combined->unique('id');

            $amount = 0.00;
            $fpxno = implode(',', $unique->pluck('transac_no')->all());
            $transaction_date = implode(',', $unique->pluck('datetime_created')->all());
            $yuran = implode(",\n", $combined->pluck('yuran')->all());
            //dd($unique);
            //dd($combined);
            foreach ($combined as $key => $tran)
            {
               
                $amount = $amount + $tran->yuranAmount;
                
                // if ($key == 0)
                // {
                //     //$fpxno = $fpxno . $tran->transac_no;
                   
                //     //$yuran=$yuran.$tran->yuran;
                // }
                // else
                // {  
                
                //     //$fpxno = $fpxno . ', '.$tran->transac_no;
                //     //$yuran=$yuran.",\n".$tran->yuran;
                // }
                
            }
            //$yuran = str_replace("\n", "<br>", $yuran);
            $data->amount = $amount == 0.00 ? 'RM 0.00' : 'RM ' . number_format($amount, 2, '.', '');
            $data->fpxno = $fpxno == '' ? $fpxno : '`' . $fpxno;
            $data->date = $transaction_date==''? $transaction_date:'`'. $transaction_date;
            $data->yuran=$yuran ;

            unset($data->userId);
            unset($data->sid);
            unset($data->oid);
            unset($data->start_date);
        }

        return $datas;
    }

    public function getData(){
        $this_start_date = $this->start_date;
        $this_end_date = $this->end_date;
        if($this->kelas!=0){
            $datas = DB::table('students as s')
            ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
            ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
            ->leftJoin('users as u', 'u.id', 'ou.user_id')
            ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
            ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
            ->leftJoin('classes as c', 'c.id', 'co.class_id')
            ->where('c.id', $this->kelas)
            ->where(function($query) use ($this_start_date, $this_end_date) {
                $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                      ->orWhere(function($query) use ($this_end_date) {
                          $query->whereNull('cs.end_date')
                                ->where('cs.start_date', '<=', $this_end_date);
                      });
            })
            ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid','cs.start_date')
            ->orderBy('s.nama')
            ->get();
            //dd($datas,$this_end_date,$this_start_date);
        }
        else{
            if($this->org->type_org==10){
                $datas = DB::table('students as s')
                ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
                ->leftJoin('users as u', 'u.id', 'ou.user_id')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->leftJoin('classes as c', 'c.id', 'co.class_id')
                ->where('co.organization_id',$this->org->parent_org)
                ->where(function($query) use ($this_start_date, $this_end_date) {
                    $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                          ->orWhere(function($query) use ($this_end_date) {
                              $query->whereNull('cs.end_date')
                                    ->where('cs.start_date', '<', $this_end_date);
                          });
                })
                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid','cs.start_date')
                ->orderBy('c.nama')
                ->orderBy('s.nama')
                ->get();
            }
            else{
                $datas = DB::table('students as s')
                ->leftJoin('organization_user_student as ous', 'ous.student_id', 's.id')
                ->leftJoin('organization_user as ou', 'ou.id', 'ous.organization_user_id', 'ou.id')
                ->leftJoin('users as u', 'u.id', 'ou.user_id')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->leftJoin('classes as c', 'c.id', 'co.class_id')
                ->where('co.organization_id',$this->oid)
                ->where(function($query) use ($this_start_date, $this_end_date) {
                    $query->whereBetween('cs.start_date', [$this_start_date, $this_end_date])
                          ->orWhere(function($query) use ($this_end_date) {
                              $query->whereNull('cs.end_date')
                                    ->where('cs.start_date', '<', $this_end_date);
                          });
                })
                ->select('s.nama', 'c.nama as nama_kelas', 's.gender', 'u.name as username', 'u.id as userId', 'co.organization_id as oid', 's.id as sid','cs.start_date')
                ->orderBy('c.nama')
                ->orderBy('s.nama')
                ->get();
            }
           
            
        }
         return $datas;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Kelas',
            'Jantina',
            'Nama Penjaga',
            'Jumlah Pembayaran Penjaga',
            'No Transaksi FPX',
            'Tarikh Transaksi',
            'Yuran yang terlibat'
        ];
    }
}
