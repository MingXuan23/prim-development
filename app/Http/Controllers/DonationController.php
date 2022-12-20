<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Donation;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\DonationRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class DonationController extends Controller
{
    private $user;
    private $donation;
    private $transaction;
    private $organization;

    public function __construct(User $user, Donation $donation, Transaction $transaction, Organization $organization)
    {
        $this->user = $user;
        $this->donation = $donation;
        $this->transaction = $transaction;
        $this->organization = $organization;
    }

    public function index()
    {
        $organization = $this->getOrganizationByUserIdWithRole();
        return view('donate.index', compact('organization'));
    }

    public function indexDerma()
    {
        $organization = $this->listAllOrganization();
        return view('paydonate.index', compact('organization'));
    }

    public function getDonationByOrganizationDatatable(Request $request)
    {
        if (request()->ajax()) {
            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {
                if ($hasOrganizaton == "false") {
                    $data = DB::table('organizations')
                    ->join('donation_organization', 'donation_organization.organization_id', '=', 'organizations.id')
                    ->join('donations', 'donations.id', '=', 'donation_organization.donation_id')
                    ->select('organizations.id as oid', 'donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url')
                    ->where('organizations.id', $oid)
                    ->where('donations.status', 1)
                    ->orderBy('donations.nama');
                } else {
                    $data = DB::table('organizations')
                    ->join('donation_organization', 'donation_organization.organization_id', '=', 'organizations.id')
                    ->join('donations', 'donations.id', '=', 'donation_organization.donation_id')
                    ->select('organizations.id as oid', 'donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url')
                    ->where('organizations.id', $oid)
                    ->orderBy('donations.nama');
                }
            }
            elseif ($hasOrganizaton == "false") {
                $data = DB::table('donations')
                    ->join('donation_organization', 'donation_organization.donation_id', '=', 'donations.id')
                    ->join('organizations', 'organizations.id', '=', 'donation_organization.organization_id')
                    ->select('organizations.id as oid', 'donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status', 'donations.url')
                    ->where('donations.status', 1)
                    ->orderBy('donations.nama');
            }
            elseif ($hasOrganizaton == "true") {
                $data = DB::table('organizations')
                    ->join('donation_organization', 'donation_organization.organization_id', '=', 'organizations.id')
                    ->join('donations', 'donations.id', '=', 'donation_organization.donation_id')
                    ->join('organization_user', 'organization_user.organization_id', '=', 'organizations.id')
                    ->join('users', 'users.id', '=', 'organization_user.user_id')
                    ->select('donations.id', 'donations.nama', 'donations.description', 'donations.date_started', 'donations.date_end', 'donations.status')
                    ->distinct()
                    ->where('users.id', $userId)
                    ->orderBy('donations.nama');
            }

            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';

                    return $btn;
                } else {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';

                    return $btn;
                }
            });

            if ($hasOrganizaton == "false") {
                $table->addColumn('URL', function ($row) {
                    $token = csrf_field();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<input type="text" readonly id="'. $row->id .'" class="form-control" value="'. URL::action('DonationController@urlDonation', array('link' => $row->url)) .'">
                    <div class="input-group-append">
                    <button id="btncopy"  onclick="copyToClipboard('. $row->id .')" class="btn btn-primary">Copy</button>
                    </div></div>';
                    return $btn;
                });
                $table->addColumn('action', function ($row) {
                    $token = csrf_field();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('paydonate', ['id' => $row->id]) . ' " class="btn btn-success m-1">Bayar</a></div>';
                    return $btn;
                });
            } else {
                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = '<a href="' . route('donate.details', $row->id) . '" class="btn btn-primary m-1">Details</a>';
                    $btn = $btn . '<a href="' . route('donation.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });
            }
            $table->editColumn('date_started', function ($row) {
                //convert to 12 hour format
                return date('d/m/Y', strtotime($row->date_started));
            });
            $table->editColumn('date_end', function ($row) {
                //convert to 12 hour format
                return date('d/m/Y', strtotime($row->date_end));
            });
            $table->rawColumns(['status', 'URL', 'action']);
            return $table->make(true);
        }
        // return Donation::geturl();
    }

    public function listAllDonor($id)
    {
        $donation = $this->donation->getDonationById($id);

        return view('donate.donor', compact('donation'));
    }

    public function getDonorDatatable(Request $request)
    {
        $donor = $this->transaction->getDonorByDonationId($request->id);

        if (request()->ajax()) {
            return datatables()->of($donor)
                ->editColumn('amount', function ($data) {
                    return number_format($data->amount, 2);
                })
                ->editColumn('datetime_created', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->datetime_created)->format('d/m/Y');
                    return $formatedDate;
                })
                ->make(true);
        }
    }

    public function historyDonor()
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            return view('errors.400');
        }

        return view('donate.history');
    }

    public function getHistoryDonorDT(Request $request)
    {
        if(!isset($request->startDate) && !isset($request->endDate))
        {
            $listhistory = DB::table('transactions')
                ->leftJoin('donation_transaction', 'transactions.id', 'donation_transaction.transaction_id')
                ->leftJoin('donations', 'donations.id', 'donation_transaction.donation_id')
                ->select('donations.nama as dname', 'transactions.amount', 'transactions.status', 'transactions.username', 'transactions.telno', 'transactions.email', 'transactions.datetime_created')
                ->where('transactions.status', 'success')
                ->where('transactions.nama','LIKE','Donation%')
                ->orderBy('transactions.datetime_created', 'desc')
                ->get();
        }
        else{
            $start_date = date('Y-m-d', strtotime($request->startDate));
            $end_date = date('Y-m-d', strtotime("+1 day", strtotime($request->endDate)));

            $listhistory = DB::table('transactions')
                ->leftJoin('donation_transaction', 'transactions.id', 'donation_transaction.transaction_id')
                ->leftJoin('donations', 'donations.id', 'donation_transaction.donation_id')
                ->select('donations.nama as dname', 'transactions.amount', 'transactions.status', 'transactions.username', 'transactions.telno', 'transactions.email', 'transactions.datetime_created')
                ->where('transactions.status', 'success')
                ->where('transactions.nama','LIKE','Donation%')
                ->whereBetween('transactions.datetime_created', [$start_date, $end_date])
                ->orderBy('transactions.datetime_created', 'desc')
                ->get();
        }

        if (request()->ajax()) {
            return datatables()->of($listhistory)
                ->editColumn('datetime_created', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->datetime_created)->format('d/m/Y');
                    return $formatedDate;
                })
                ->editColumn('amount', function ($data) {
                    return number_format($data->amount, 2);
                })->make(true);
        }
    }

    public function listAllOrganization()
    {
        $organization = Organization::get();
        return $organization;
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();

        $listorg = Organization::whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return $listorg;
    }

    public function getOrganizationByUserIdWithRole()
    {
        $userId = Auth::id();

        $listorg = Organization::whereHas('user', function ($query) use ($userId) {
            $query->where('user_id', $userId)->whereIn('role_id', [1, 2, 4]);
        })->get();
        return $listorg;
    }

    public static function getDonationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {
            return Organization::select('*')
            ->join('donation_organization', 'organizations.id', '=', 'donation_organization.organization_id')
            ->join('donations', 'donation_organization.donation_id', '=', 'donations.id')
            ->orderBy('donations.nama')
            ->get();
        } else {
            return Organization::select('*')
            ->leftjoin('organization_user', 'organizations.id', '=', 'organization_user.organization_id')
            ->leftjoin('users', 'organization_user.user_id', '=', 'users.id')
            ->leftjoin('donation_organization', 'organizations.id', '=', 'donation_organization.organization_id')
            ->leftjoin('donations', 'donation_organization.donation_id', '=', 'donations.id')
            ->where('users.id', $userId)
            ->where('organization_user.role_id', 2)
            // ->distinct('donations.nama')
            ->get();
        }
    }

    public function urlDonation($link)
    {
        $user = "";

        //$donation = Donation::where('url', $link)->first();
        $donation = DB::table('donations')
                        ->where('url', '=' , $link)
                        ->first();
        // dd($donation);

        if($donation->status == 0)
        {
            return view('errors.404');
        }

        if (Auth::id()) {
            $user = $this->user->getUserById();
        }

        return view('paydonate.pay', compact('donation', 'user'));
    }

    public function anonymouIndex($link)
    {
        $donation = DB::table('donations')
                        ->where('url', '=' , $link)
                        ->first();

        if($donation->status == 0)
        {
            return view('errors.404');
        }

        return view('paydonate.anonymous.index', compact('donation'));
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();
        $donation_type = DB::table('donation_type')->orderBy('nama')->get();

        return view('donate.add', compact('organization', 'donation_type'));
    }

    public function store(DonationRequest $request)
    {
        $link = explode(" ", $request->nama);
        $str = implode("-", $link);

        $start_date = Carbon::createFromFormat(config('app.date_format'), $request->date_started)->format('Y-m-d');
        $end_date = Carbon::createFromFormat(config('app.date_format'), $request->date_end)->format('Y-m-d');

        $file_name = '';

        if (!is_null($request->donation_poster)) {
            $storagePath  = $request->donation_poster->storeAs('public/donation-poster', 'donation-poster-'.time().'.jpg');
            $file_name = basename($storagePath);
        }

        $donation = Donation::create($request->validated() + [
            'date_created'      => now(),
            'date_started'      => $start_date,
            'date_end'          => $end_date,
            'status'            => '1',
            'url'               => $str,
            'donation_poster'   => $file_name,
            'donation_type'     => $request->donation_type
        ]);

        Donation::where('id', $donation->id)->update(['code' => $this->generateDonationCode($donation->id, $request->donation_type)]);

        $donation->organization()->attach($request->organization);

        return redirect('/donation')->with('success', 'Derma Berjaya Ditambah');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $organizations = $this->getOrganizationByUserId();
        $organization = $this->organization->getOrganizationByDonationId($id);
        $donation = DB::table('donations')->where('id', $id)->first();
        $donation_type = DB::table('donation_type')->orderBy('nama')->get();
        $current_type = DB::table('donation_type')
                        ->where('id', '=', $donation->donation_type)
                        ->select('nama')
                        ->first();
        // dd($current_type);

        return view('donate.update', compact('donation', 'organization', 'organizations', 'donation_type', 'current_type'));
    }

    public function update(DonationRequest $request, $id)
    {
        $this->validate($request, [
            'nama'              => 'required',
            'donation_type'     => 'required',
            'date_started'      => 'required',
            'date_end'          => 'required',
            'donation_poster'   => 'required'
        ]);

        $link = explode(" ", $request->nama);
        $str = implode("-", $link);

        $start_date = Carbon::createFromFormat(config('app.date_format'), $request->date_started)->format('Y-m-d');
        $end_date = Carbon::createFromFormat(config('app.date_format'), $request->date_end)->format('Y-m-d');

        $file_name = '';

        // dd($request->donation_type);

        if (!is_null($request->donation_poster)) {

            // Delete existing image before update with new image;
            $donation = $this->donation->getDonationById($id);

            if (config('app.env') == 'staging' ||config('app.env') == 'production' )
            {
                $destination = public_path('donation-poster') . '/' . $donation->donation_poster;
                unlink($destination);
            }

            $storagePath  = $request->donation_poster->storeAs('public/donation-poster', 'donation-poster-'.time().'.jpg');
            $file_name = basename($storagePath);
        }

        DB::table('donations')
            ->where('id', $id)
            ->update([
                'nama'                => $request->nama,
                'date_created'        => now(),
                'date_started'        => $start_date,
                'date_end'            => $end_date,
                'status'              => '1',
                'url'                 => $str,
                'donation_poster'     => $file_name,
                'donation_type'       => $request->donation_type,
                'lhdn_reference_code' => $request->lhdn_reference_code
            ]);

        return redirect('/donation')->with('success', 'Derma Telah Berjaya Dikemaskini');
    }

    public function destroy($id)
    {
        $result = DB::table('donations')
                    ->where('id', '=', $id)
                    ->update(
                        ['status' => 0]
                    );

        if ($result) {
            Session::flash('success', 'Derma Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Derma Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function generateDonationCode($id, $donation_type)
    {
        switch($donation_type)
        {
            case 1:
                $code = 'STU' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 2:
                $code = 'FB' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 3:
                $code = 'STEM' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 4:
                $code = 'THFZ' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 5:
                $code = 'MJD' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 6:
                $code = 'RIBD' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 7:
                $code = 'NGO' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;

            case 8:
                $code = 'LA' . str_pad($id, 3, '0', STR_PAD_LEFT);
                break;
        }

        return $code;
    }

    public function indexLHDN()
    {
        if (Auth::user()->hasRole('Superadmin') || Auth::user()->hasRole('Admin LHDN'))
        {
            $donations = Donation::where('lhdn_reference_code', '!=', null)->get();
            return view('lhdn.index', compact('donations'));
        }

        return view('errors.404');
    }

    public function getLHDNHistoryDatatable(Request $request)
    {
        $listhistory = DB::table('transactions')
            ->leftJoin('donation_transaction', 'transactions.id', 'donation_transaction.transaction_id')
            ->leftJoin('donations', 'donations.id', 'donation_transaction.donation_id')
            ->select('transactions.id as id', 'donations.nama as dname', 'transactions.amount', 'transactions.status', 'transactions.username', 'transactions.telno', 'transactions.email', 'transactions.datetime_created')
            ->where('donations.id', $request->dermaId)
            ->where('transactions.icno', '!=', null)
            ->where('transactions.status', 'success')
            ->orderBy('transactions.datetime_created', 'desc')
            ->get();

        if (request()->ajax()) {
            return datatables()->of($listhistory)
                ->editColumn('datetime_created', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->datetime_created)->format('d/m/Y');
                    return $formatedDate;
                })
                ->editColumn('amount', function ($data) {
                    return number_format($data->amount, 2);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a class="btn btn-primary" href="' . route('lhdn-receipt', $row->id) .'">papar resit</a></div>';
    
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    public function getLHDNReceipt($id)
    {
        $transaction = Transaction ::find($id);
        $donation = DB::table('donations as d')
            ->leftJoin('donation_transaction as dt', 'dt.donation_id', 'd.id')
            ->where('dt.transaction_id', $id)
            ->select('d.*')
            ->first();

        if(!isset($donation->lhdn_reference_code))
        {
            return view('errors.404');
        }
        
        $organization = $this->organization->getOrganizationByDonationId($donation->id);
        return view('lhdn.receipt', compact('donation', 'organization', 'transaction'));
    }
}
