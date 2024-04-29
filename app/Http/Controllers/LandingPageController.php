<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Transaction;
use App\Models\Donation;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LandingPageController extends AppBaseController
{
    private $organization;
    private $donation;

    public function __construct(Organization $organization, Donation $donation)
    {
        $this->organization = $organization;
        $this->donation = $donation;
    }

    public function index()
    {
        // return view('landing-page.index');
        // return view('custom-errors.500');
        return view('custom-errors.maintenance');
    }

    //wan add
    public function indexPrim()
    {
        //$schoollist = '';

        $schools = DB::table('organization_url')
            ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
            ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
            ->whereIn('type_organizations.id', [1, 2, 3])
            ->where('organization_url.title', 'NOT LIKE', '%Poli%')
            ->get();

        //dd($schools);

        $politeknik = DB::table('organization_url')
        ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        ->whereIn('type_organizations.id', [1, 2, 3])
        ->where('organization_url.title', 'LIKE', '%Poli%')
        ->get();

        //dd($politeknik);

        return view('landing-page.prim.index', ['schools' => $schools], ['politeknik' => $politeknik]);
    }
    //end wan add

    //edit by wan
    public function indexFees()
    {
        $organization = DB::table('organization_url as url')
            ->join('organizations as o', 'url.organization_id', '=', 'o.id')
            ->where('url.status',1)
            ->whereIn('o.type_org', [1, 2, 3])
            ->get();

        // $schools = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        //     ->whereIn('type_organizations.id', [1, 2, 3])
        //     ->where('organization_url.title', 'NOT LIKE', '%Poli%')
        //     ->get();

        // //dd($schools);

        // $politeknik = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->join('type_organizations', 'organizations.type_org', '=', 'type_organizations.id')
        //     ->whereIn('type_organizations.id', [1, 2, 3])
        //     ->where('organization_url.title', 'LIKE', '%Poli%')
        //     ->get();

        return view('landing-page.fees.index', ['organizations' => $organization]);
    }
    //end edit by wan

    public function storeMessage(Request $request)
    {
        // dd($request);
        $this->validate($request, [
            'uname'         =>  'required',
            'email'         =>  'required | email',
            'message'       =>  'required',
            'telno'         =>  'required',
        ]);

        $feedback = Feedback::create([
            'name'      => $request->get('uname'),
            'email'     => $request->get('email'),
            'telno'     => $request->get('telno'),
            'message'   => $request->get('message'),
        ]);

        return redirect()->back()->with('alert', 'Terima kasih');
    }

    //edit by wan
    // public function organizationList()
    public function indexOrganizationList()
    {
        // $organization = DB::table('organization_url')
        //     ->join('organizations', 'organization_url.organization_id', '=', 'organizations.id')
        //     ->get();
            
        // return view('landing-page.organization_list', ['organizations' => $organization]);
        return view('landing-page.organization_list');
    }
    //end edit by wan

    public function activitylist()
    {
        return view('landing-page.listactivity');
    }

    public function activitydetails()
    {
        return view('landing-page.activitydetails');
    }

    // public function getDonationDatatable()
    // {
    //     $data = DB::table('donations')
    //         ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
    //         ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
    //         ->select('donations.id', 'donations.nama as nama_derma', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url', 'organizations.nama as nama_organisasi', 'organizations.email', 'organizations.address')
    //         ->where('donations.status', 1)
    //         ->orderBy('donations.nama')
    //         ->get();

    //     $table = Datatables::of($data);

    //     $table->addColumn('action', function ($row) {
    //         $btn = '<div class="d-flex justify-content-center">';
    //         $btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="btn btn-success m-1">Bayar</a></div>';
    //         return $btn;
    //     });
    //     $table->rawColumns(['action']);
    //     return $table->make(true);
    // }

    // ********************************Landing page Donation**********************************

    public function indexDonation()
    {
        $organization = Organization::all()->count();
        $curYear = date("Y") . "-01-01";

        $transactions = Transaction::where('nama', 'LIKE', 'Donation%')
            ->where('status', 'Success')
            ->where('datetime_created', '>', $curYear)
            ->get()->count();

        // retrieve daily transactions
        $dailyTransactions = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', date('Y-m-d'))
            ->get()->count();

        $totalAmount = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', $curYear)
            ->select(DB::table('transactions')->raw('sum(amount) as total_amount'))
            ->first();

        $dailyGain = DB::table('transactions')
            ->where('status', 'success')
            ->where('nama', 'LIKE', 'donation%')
            ->where('datetime_created', '>', date('Y-m-d'))
            ->select(DB::table('transactions')->raw('sum(amount) as total_amount'))
            ->first();

        $dailyGain = $dailyGain->total_amount;

        $totalAmount = (int) $totalAmount->total_amount;

        // dd($totalAmount);

        /* 
            SELECT SUM(amount) AS "Total Amount"
            FROM transactions	
            WHERE datetime_created > CURDATE()
            AND `nama` LIKE "Donation%"
            AND `status` = "success";
        */

        $donation = DB::table('donations')
            ->where('status', 1)
            ->get()
            ->count();

        $oneWeekBeforeToday = date_create(date('Y-m-d'));
        date_sub($oneWeekBeforeToday,date_interval_create_from_date_string('7 days'));
        $donors = Transaction::where('nama','LIKE' , 'Donation%')
        ->where('status','Success')
        ->where(function($query) use ($oneWeekBeforeToday){
            $query->whereDate('datetime_created', '<=', date('Y-m-d'));
            // ->where('datetime_created' , '>' , $oneWeekBeforeToday);

        })
        ->orderBy('datetime_created' ,'desc')
        ->orderBy('amount' ,'desc')
        ->take(20)
        ->get();
        // if(count($donors) < 20){
        //     // Calculate how many times we need to duplicate the collection
        //     $timesToDuplicate = 20 - count($donors);
        //     $j = 0;
        //     // Duplicate the collection
        //     for ($i = 0; $i < $timesToDuplicate; $i++) {
        //         if($j >= count($donors)){
        //             $j = 0;
        //         }else{
        //             $donors->push($donors[$i]);
        //             $j++;
        //         }
        //     }
        // }

        session()->forget('intendedUrl');//reset intended url for point system 
        return view('landing-page.donation.index', compact('organization', 'transactions', 'donation', 'dailyGain', 'dailyTransactions', 'totalAmount' ,'donors'));
    }

    public function organizationListDonation()
    {
        return view('landing-page.donation.organization_list');
    }

    public function activitylistDonation()
    {
        return view('landing-page.donation.listactivity');
    }

    public function activitydetailsDonation()
    {
        return view('landing-page.donation.activitydetails');
    }

    public function getOrganizationByType($type)
    {
        try {
            $organizations = $this->organization->getOrganizationByType($type);
            return $organizations;
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getDonationByOrganizationId($id)
    {
        try {
            $donations = $this->donation->getDonationByOrganizationId($id);
            return $donations;
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getOrganizationByDonationId($id)
    {
        try {
            $organizations = $this->organization->getOrganizationByDonationId($id);
            return $organizations;
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getOrganizationDatatable(Request $request)
    {
        $data = $this->getOrganizationByType($request->type);

        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) {
            $btn = '<div class="d-flex justify-content-center">';
            // $btn = $btn . '<a class="btn btn-outline-primary waves-effect waves-light btn-sm btn-donation" data-toggle="modal" data-target=".modal-derma" id="'. $row->id . '">Derma</a></div>';
            $btn = $btn . '<a href="#" class="boxed-btn btn-rounded btn-donation" data-toggle="modal" data-target=".modal-derma" id="' . $row->id . '" style="color: white;">Derma</a></div>';

            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function getDonationDatatable(Request $request)
    {
        $data = $this->getDonationByOrganizationId($request->id);

        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) {
            $btn = '<div class="d-flex justify-content-center">';
            //$btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="boxed-btn btn-rounded btn-donation">Bayar</a></div>';
            $btn = $btn . '<a href="#" class="boxed-btn btn-rounded btn-donation" data-toggle="modal" data-target=".modal-derma" id="' . $row->id . '" style="color: white;">Derma</a></div>';
            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function customOrganizationTabbing(Request $request)
    {
        // dd($request->type);
        $data = Donation::where('donations.donation_type', $request->type)
            ->where('donations.status', 1)
            ->get();
        $table = Datatables::of($data);
        $table->addColumn('email', function ($row) {
            $data1 = $this->getOrganizationByDonationId($row->id);
            $data2 = $data1->email;
            return $data2;
        });
        $table->addColumn('telno', function ($row) {
            $data1 = $this->getOrganizationByDonationId($row->id);
            $data2 = $data1->telno;
            return $data2;
        });
        $table->addColumn('action', function ($row) {
            // dd($row->url);
            $btn = '<div class="d-flex justify-content-center">';
            // $btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="boxed-btn btn-rounded btn-donation">Jom&nbsp;Derma</a></div>';
            $btn = $btn . '<a href="' . route('URLdonate', ['link' => $row->url]) . ' " class="boxed-btn btn-rounded btn-donation">Derma Dengan Nama</a></div>';
            $btn = $btn . '<div class="d-flex justify-content-center"><a href="' . route('ANONdonate', ['link' => $row->url]) . ' " class="boxed-btn btn-rounded btn-donation2">Derma Tanpa Nama</a></div>';
            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function getDonationByTabbing(Request $request)
    {
        if ($request->ajax()) {
            $posters = '';


            $donations = DB::table('donations')
                ->where('donations.donation_type', $request->type)
                ->where('donations.status', 1)
                ->inRandomOrder()
                ->get();

            $currentYear = date('Y');
            

            foreach ($donations as $donation) {
                // to get the total amount of donation for each donation posters
                $amountDonation = DB::table('donation_transaction as dt')->
                join('transactions as t', 't.id' , 'dt.transaction_id')
                ->where([
                    't.status' => 'Success',
                    'dt.donation_id' => $donation->id,
                ])
                ->whereYear('t.datetime_created', $currentYear)
                ->sum('t.amount');

                $previousDonation = DB::table('donation_transaction as dt')->
                join('transactions as t', 't.id' , 'dt.transaction_id')
                ->where([
                    't.status' => 'Success',
                    'dt.donation_id' => $donation->id,
                ])
                ->whereYear('t.datetime_created', $currentYear -1)
                ->sum('t.amount');
                
                $posters = $posters . '<div class="card"> <div class="donation-amount">Tahun '.$currentYear.':<b> RM'.number_format($amountDonation,2).'</b></div>';
                $posters = $posters. '<div class="donation-amount">Tahun '.($currentYear -1).':<b> RM'.number_format($previousDonation,2).'</b></div><img class="card-img-top donation-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap" loading="lazy">';
                $posters = $posters . '<div class="card-body"><div class="d-flex flex-column justify-content-center ">';
                $posters = $posters . '<a href="' . route('URLdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation">Derma Dengan Nama</a></div>';
                $posters = $posters . '<div class="d-flex justify-content-center"><a href="' . route('ANONdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation2">Derma Tanpa Nama</a></div></div>
                </div>';
            }

            if ($posters === '') {
                return '';
                // return '<div class="d-flex justify-content-center">Tiada Makulmat Dipaparkan</div>';
            }

            return $posters;
        }
    }

    public function getHeaderPoster()
    {
        $posters = '';

        $donations = DB::table('donations')
            ->where('donations.status', 1)
            ->inRandomOrder()
            ->limit(5)
            ->get();

            $currentYear = date('Y');

        foreach ($donations as $donation) {
            $amountDonation = DB::table('donation_transaction as dt')->
            join('transactions as t', 't.id' , 'dt.transaction_id')
            ->where([
                't.status' => 'Success',
                'dt.donation_id' => $donation->id,
            ])
            ->whereYear('t.datetime_created', $currentYear)
            ->sum('t.amount');

            $previousDonation = DB::table('donation_transaction as dt')->
            join('transactions as t', 't.id' , 'dt.transaction_id')
            ->where([
                't.status' => 'Success',
                'dt.donation_id' => $donation->id,
            ])
            ->whereYear('t.datetime_created', $currentYear -1)
            ->sum('t.amount');

            $posters = $posters . '<div class="card"> <div class="donation-amount">Tahun '.$currentYear.':<b> RM'.number_format($amountDonation,2).'</b></div> ';
            $posters = $posters .'<div class="donation-amount">Tahun '.($currentYear -1).':<b> RM'.number_format($previousDonation,2).'</b></div>';
            $posters = $posters. '<a href="' . route('ANONdonate', ['link' => $donation->url]) . '">';
            $posters = $posters . '<img class="card-img-top header-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap" loading="lazy"></a></div>';
        }

        if ($posters === '') {
            return '';
        }

        return $posters;
    }
}
