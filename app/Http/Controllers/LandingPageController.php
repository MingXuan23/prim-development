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
        $transactions = Transaction::where('nama', 'LIKE', 'Donation%')
            ->where('status', 'Success')
            ->get()->count();
        $donation = Donation::all()->count();

        // dd($transactions->count());
        return view('landing-page.donation.index', compact('organization', 'transactions', 'donation'));
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
            $btn = $btn . '<a href="#" class="boxed-btn btn-rounded btn-donation" data-toggle="modal" data-target=".modal-derma" id="'. $row->id . '" style="color: white;">Derma</a></div>';

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
            $btn = $btn . '<a href="#" class="boxed-btn btn-rounded btn-donation" data-toggle="modal" data-target=".modal-derma" id="'. $row->id . '" style="color: white;">Derma</a></div>';
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
        $table->addColumn('email', function($row){
            $data1 = $this->getOrganizationByDonationId($row->id);
            $data2 = $data1->email;
            return $data2;
        });
        $table->addColumn('telno', function($row){
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
}
