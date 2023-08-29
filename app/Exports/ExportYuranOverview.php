<?php

namespace App\Exports;
use stdClass;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Symfony\Component\VarDumper\Cloner\Data;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportYuranOverview implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct($orgId)
    {
        $this->orgId = $orgId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $fee=DB::table('fees_new as fn')
            ->where('fn.organization_id',$this->orgId)
            ->where('fn.status',1)
            ->orderBy('fn.category')
            ->get();
        //dd($fee);
        $data=[];
        foreach($fee as $key => $fn){
            if($fn->category=="Kategory A"){
                $feedata = new stdClass();
                $parent=DB::table('fees_new_organization_user as fou')
                        ->join('organization_user as ou','ou.id','fou.organization_user_id')
                        ->join('users as u','u.id','ou.user_id')
                        ->leftJoin('transactions as t','t.id','fou.transaction_id')
                        ->where('fou.fees_new_id',$fn->id)  
                        ->where('ou.status',"1")
                        ->select('fou.*','t.status as tstatus')
                        ->distinct()
                        ->get();

                $paid=$parent->where('status',"Paid")->where('tstatus',"Success");
                $feedata->no=$key+1;
                $feedata->yuranCategory=$fn->category;
                $feedata->yuranName=$fn->name;
                $feedata->price=$fn->price;
                $feedata->paidNumber=count($paid);
                $feedata->estimateNumber=count($parent);
                $feedata->totalIncome=$fn->price*$fn->quantity*$feedata->paidNumber;
                $feedata->estimateIncome=$fn->price*$fn->quantity*$feedata->estimateNumber;
                //dd($feedata);
                array_push($data, $feedata);
                
            }
            else{
                $feedata = new stdClass();
                $student= DB::table('student_fees_new as sfn')
                        ->leftJoin('fees_transactions_new as ftn','ftn.student_fees_id','sfn.id')
                        ->leftJoin('transactions as t','t.id','ftn.transactions_id')
                        ->where('sfn.fees_id',$fn->id) 
                        ->select('sfn.*','t.status as tstatus')
                        ->distinct()
                        ->get();
                        
                $paid=$student->where('status',"Paid")->where('tstatus',"Success");
                $feedata->no=$key+1;

                $feedata->yuranCategory=$fn->category;

                $feedata->yuranName=$fn->name;
                
                $feedata->price=$fn->price;
                $feedata->paidNumber=count($paid);
                $feedata->estimateNumber=count($student);
                $feedata->totalIncome=$fn->price*$fn->quantity*$feedata->paidNumber;
                $feedata->estimateIncome=$fn->price*$fn->quantity*$feedata->estimateNumber;
                //dd($feedata);
                array_push($data, $feedata);
            }
        }

        //overview yuran for all
        $feedata = new stdClass();
        $feedata->no="*";
        $feedata->yuranCategory="All";

        $feedata->yuranName="All Yuran";
        
        $feedata->price="-";

        $org=DB::table('organizations as o')
        ->where('o.id',$this->orgId)
        ->first();

        $orgID=$org->id;
        if($org->parent_org!=null ){
            $orgID=$org->parent_org;
        }

        $student=DB::table('organization_user as ou')
                ->leftJoin('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
                ->leftJoin('students as s', 's.id', 'ous.student_id')
                ->leftJoin('class_student as cs', 'cs.student_id', 's.id')
                ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                ->leftJoin('classes as c','c.id','co.class_id')
                ->where('ou.organization_id', $orgID)
                ->selectRaw('count(distinct s.id) as count')
                ->first();

        $parent =DB::table('organization_user as ou')
                ->where('ou.organization_id',$this->orgId)
                ->where('ou.role_id',6)
                ->where('ou.status',1)
                ->selectRaw('count(distinct ou.user_id) as count')
                ->first();
        
        $transactionA=DB::table('transactions as t')
                    ->join('fees_new_organization_user as fou','fou.transaction_id','t.id')
                    ->join('fees_new as fn','fn.id','fou.fees_new_id')
                    ->where('fn.organization_id',$this->orgId)
                    ->where('t.status',"Success")
                    ->where('fn.status',1)
                    ->select('t.*')
                    ->distinct('t.id')
                    ->get();

        $transactionB=DB::table('transactions as t')
                    ->join('fees_transactions_new as ftn','ftn.transactions_id','t.id')
                    ->join('student_fees_new as sfn','ftn.student_fees_id','sfn.id')
                    ->join('fees_new as fn','fn.id','sfn.fees_id')
                    ->where('t.status',"Success")
                    ->where('fn.organization_id',$this->orgId)
                    ->where('fn.status',1)
                    ->select('t.*')
                    ->distinct('t.id')
                    ->get();
        $combinedTransactions = $transactionA->merge($transactionB);

        $transaction = $combinedTransactions->pluck('id')->unique();

        $totalAmount = DB::table('transactions as t')
            ->whereIn('t.id', $transaction)
            ->sum('t.amount');

        $feedata->paidNumber= count($transaction);
        $feedata->estimateNumber="P:".$parent->count.",S:".$student->count;

        $feedata->totalIncome=$totalAmount;
        $feedata->estimateIncome="-";
        //dd($feedata);
        array_push($data, $feedata);
        //$data=
        return collect($data);
    }

    public function headings(): array
    {

        return [
            'No',
            'Kategori Yuran',
            'Nama Yuran',
            'Harga Yuran',
            'Jumlah Penjaga/Murid Telah Bayar',
            'Jumlah Penjaga/Murid Perlu Bayar',
            'Jumlah Pendapatan',
            'Jangkaan Pendapatan'
        ];
    }
}
