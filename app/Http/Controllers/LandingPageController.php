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

    public function indexFees()
    {
        return view('landing-page.fees.index');
    }

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

    public function organizationList()
    {
        return view('landing-page.organization_list');
    }

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

        // dd($donation);
        return view('landing-page.donation.index', compact('organization', 'transactions', 'donation', 'dailyGain', 'dailyTransactions', 'totalAmount'));
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

            foreach ($donations as $donation) {
                $posters = $posters . '<div class="card"> <img class="card-img-top donation-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap">';
                $posters = $posters . '<div class="card-body"><div class="d-flex flex-column justify-content-center ">';
                
                if ($donation->lhdn_status == 0)
                {
                    $posters = $posters . '<a href="' . route('URLdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation">Derma Dengan Nama</a></div>';
                }
                else if ($donation->lhdn_status == 1)
                {
                    $posters = $posters . '<a href="' . route('LHDNdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation">Derma Pengecualian Cukai</a></div>';
                }

                $posters = $posters . '<div class="d-flex justify-content-center"><a href="' . route('ANONdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation2">Derma Tanpa Nama</a></div></div></div>';
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

        foreach ($donations as $donation) {
            $posters = $posters . '<div class="card"><a href="' . route('ANONdonate', ['link' => $donation->url]) . '">';
            $posters = $posters . '<img class="card-img-top header-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap"></a></div>';
        }

        if ($posters === '') {
            return '';
        }

        return $posters;
    }
}
