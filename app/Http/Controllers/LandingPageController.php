<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing-page.index');
        // return view('custom-errors.500');

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

    public function getDonationDatatable()
    {
        $data = DB::table('donations')
                    ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
                    ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
                    ->select('donations.id', 'donations.nama as nama_derma', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url', 'organizations.nama as nama_organisasi', 'organizations.email', 'organizations.address')
                    ->where('donations.status', 1)
                    ->orderBy('donations.nama')
                    ->get();
        
        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) {
            $btn = '<div class="d-flex justify-content-center">';
            $btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="btn btn-success m-1">Bayar</a></div>';
            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }

    public function indexDonation()
    {
        return view('landing-page.donation.index');
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

    public function getDonationDatatableDonation()
    {
        $data = DB::table('donations')
                    ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
                    ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
                    ->select('donations.id', 'donations.nama as nama_derma', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url', 'organizations.nama as nama_organisasi', 'organizations.email', 'organizations.address')
                    ->where('donations.status', 1)
                    ->orderBy('donations.nama')
                    ->get();
        
        $table = Datatables::of($data);

        $table->addColumn('action', function ($row) {
            $btn = '<div class="d-flex justify-content-center">';
            $btn = $btn . '<a href="sumbangan/' . $row->url . ' " class="btn btn-success m-1">Bayar</a></div>';
            return $btn;
        });
        $table->rawColumns(['action']);
        return $table->make(true);
    }
}
