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

    public function getFeesByYear($year)
    {
        // Retrieve the fees for the given year
        $fee = DB::table('fees_new as fn')
            ->where('fn.organization_id', $this->orgId)
            ->whereYear('fn.start_date', $year)
            ->orderBy('fn.category')
            ->get();


        // Initialize an empty array to store valid fees
        $validFees = [];

        // Loop through each fee
        foreach ($fee as $fn) {
            $validator = false; // Default validation state

            // Check validation based on category
            if ($fn->category == "Kategori A") {
                $validator = DB::table('fees_new_organization_user as fou')
                    ->join('transactions as t', 't.id', 'fou.transaction_id')
                    ->where('fou.fees_new_id', $fn->id)
                    ->where('t.status', "Success")
                    ->exists();
            } else {
                $validator = DB::table('student_fees_new as sfn')
                    ->join('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                    ->join('transactions as t', 't.id', 'ftn.transactions_id')
                    ->where('sfn.fees_id', $fn->id)
                    ->where('t.status', "Success")
                    ->exists();
            }

            // If validation passes, add this fee to the validFees array
            if ($validator) {
                $validFees[] = $fn;
            }
        }

        // Return only valid fees
        return $validFees;
    }


    public function collection()
    {
        set_time_limit(300);
        $orgID = $this->orgId;
        // Get all years where fees exist
        $feeYear = DB::table('fees_new as fn')
            ->where('fn.organization_id', $this->orgId)
            ->select(DB::raw('YEAR(fn.start_date) as year'))
            ->groupBy('year')
            ->orderByDesc('year')
            ->pluck('year');

        $data = [];

        // Loop through each year and process fees for that year
        foreach ($feeYear as $year) {
            $fee = $this->getFeesByYear($year);

            // Display year header and initialize totals for the year
            $yearlyTotalIncome = 0;
            $yearlyEstimateIncome = 0;

            // Loop through each fee for the year
            foreach ($fee as $key => $fn) {
                $feedata = new stdClass();

                // Process fees based on category
                if ($fn->category == "Kategori A") {
                    $parent = DB::table('fees_new_organization_user as fou')
                        ->join('organization_user as ou', 'ou.id', 'fou.organization_user_id')
                        ->join('users as u', 'u.id', 'ou.user_id')
                        ->join('organization_user_student as ous', 'ous.organization_user_id', 'ou.id')
                        ->leftJoin('transactions as t', 't.id', 'fou.transaction_id')
                        ->where('fou.fees_new_id', $fn->id)
                        //->where('ou.status', "1")
                        ->select('fou.*', 't.status as tstatus')
                        ->distinct()
                        ->get();

                    $paid = $parent->where('status', "Paid")->where('tstatus', "Success");

                    $feedata->no = $key + 1;
                    $feedata->yuranCategory = $fn->category;
                    $feedata->yuranName = $fn->name;
                    $feedata->price = $fn->price;
                    $feedata->paidNumber = count($paid);
                    $feedata->estimateNumber = count($parent);
                    $feedata->totalIncome = $fn->price * $fn->quantity * $feedata->paidNumber;
                    $feedata->estimateIncome = $fn->price * $fn->quantity * $feedata->estimateNumber;
                } else {

                    $student = DB::table('student_fees_new as sfn')
                        ->leftJoin('fees_transactions_new as ftn', 'ftn.student_fees_id', 'sfn.id')
                        ->leftJoin('transactions as t', 't.id', 'ftn.transactions_id')
                        ->leftJoin('class_student as cs', 'cs.id', 'sfn.class_student_id')
                        ->leftJoin('class_organization as co', 'co.id', 'cs.organclass_id')
                        ->leftJoin('classes as c', 'c.id', 'co.class_id')
                        ->where('sfn.fees_id', $fn->id)
                        ->where('c.levelid', '>', 0)
                        ->where('cs.status', 1)

                        ->select('sfn.*', 't.status as tstatus')
                        ->orderByDesc('tstatus')
                        ->distinct('sfn.id')
                        ->get();

                    $student = $student->unique('id');

                    //dd($student);



                    $paid = $student->where('status', "Paid")->where('tstatus', "Success");

                    $feedata->no = $key + 1;
                    $feedata->yuranCategory = $fn->category;
                    $feedata->yuranName = $fn->name;
                    $feedata->price = $fn->price;
                    $feedata->paidNumber = count($paid);
                    $feedata->estimateNumber = count($student);
                    $feedata->totalIncome = $fn->price * $fn->quantity * $feedata->paidNumber;
                    $feedata->estimateIncome = $fn->price * $fn->quantity * $feedata->estimateNumber;
                }

                // Add fee data to the array
                array_push($data, $feedata);

                // Update totals for the year
                $yearlyTotalIncome += $feedata->totalIncome;
                $yearlyEstimateIncome += $feedata->estimateIncome;
            }

            // Add yearly totals to the report

            $yearSummary = new stdClass();
            $yearSummary->no = '*';
            $yearSummary->yuranCategory = "Laporan Tahun";
            $yearSummary->yuranName = "Total Tahun " . $year;
            $yearSummary->price = "-";
            $yearSummary->paidNumber = "-";
            $yearSummary->estimateNumber = "-";
            $yearSummary->totalIncome = $yearlyTotalIncome;
            $yearSummary->estimateIncome = $yearlyEstimateIncome;
            array_push($data, $yearSummary);

            $emptyRow = new stdClass();
            $emptyRow->no = '';
            $emptyRow->yuranCategory = '';
            $emptyRow->yuranName = '';
            $emptyRow->price = '';
            $emptyRow->paidNumber = '';
            $emptyRow->estimateNumber = '';
            $emptyRow->totalIncome = '';
            $emptyRow->estimateIncome = '';
            array_push($data, $emptyRow);
        }

        // Return the collected data
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
