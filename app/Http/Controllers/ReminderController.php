<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Donation;
use App\Http\Controllers\DonationController;
use App\Http\Requests\ReminderRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Auth;

class ReminderController extends Controller
{
    private $reminder;
    private $donation;

    public function __construct(Reminder $reminder, Donation $donation)
    {
        $this->reminder = $reminder;
        $this->donation = $donation;
        
        $this->middleware('ReminderResource');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donations = $this->donation->getAllDonation();
        return view("reminder.index", compact('donations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $donations = $this->donation->getAllDonation();
        return view("reminder.add", compact('donations'), ['reminder' => new Reminder()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReminderRequest $request)
    {
        $userId = Auth::id();
        $reminder = Reminder::create($request->validated());
        
        $reminder->donation()->attach($request->donation, ['user_id' => $userId]);

        return redirect('/reminder')->with('success', 'Peringatan derma telah berjaya ditambah');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $donations = $this->donation->getDonationByReminderId($id);
        $reminder = Reminder::find($id);

        return view('reminder.add')->with('reminder', $reminder)->with('donations', $donations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReminderRequest $request, $id)
    {
        Reminder::where('id', $id)->update($request->validated());

        return redirect('/reminder')->with('success', 'Peringatan derma berjaya dikemaskini');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Reminder::find($id)->delete();

        if ($result) {
            Session::flash('success', 'Peringatan derma telah berjaya dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Peringatan derma gagal untuk dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getReminderDatatable(Request $request)
    {
        $donationId = $request->donationId;

        if (request()->ajax()) {
            if (is_null($donationId)) {
                $data = $this->reminder->getAllReminder();
            } else {
                $data = $this->reminder->getReminderByDonationId($donationId);
            }

            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('reminder.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->editColumn('time', function ($response) {
                    //convert to 12 hour format
                    return date('h:i A', strtotime($response->time));
                })
                ->editColumn('recurrence', function ($response) {
                    switch ($response->recurrence) {
                        case "daily":
                            return "Harian";
                            break;
                        case "weekly":
                            return "Mingguan";
                            break;
                        case "monthly":
                            return "Bulanan";
                            break;
                        default:
                            break;
                    }
                })
                ->editColumn('day', function ($response) {
                    switch ($response->day) {
                        case 1:
                            return "Isnin";
                            break;
                        case 2:
                            return "Selesa";
                            break;
                        case 3:
                            return "Rabu";
                            break;
                        case 4:
                            return "Khamis";
                            break;
                        case 5:
                            return "Jummat";
                            break;
                        case 6:
                            return "Sabtu";
                            break;
                        case 7:
                            return "Ahad";
                            break;
                        default:
                            break;
                    }
                })
                ->make(true);
        }
    }
}
