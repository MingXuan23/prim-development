<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Http\Controllers\DonationController;
use App\Http\Requests\ReminderRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Auth;
use DB;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donations = DonationController::getAllDonation();
        return view("reminder.index", compact('donations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $donations = DonationController::getAllDonation();
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
        $donations = DonationController::getDonationByReminderId($id);
        $reminder = Reminder::find($id);

        return view('reminder.add', compact('donations'))->with('reminder', $reminder);
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

    public static function getReminderByDonationId($donationId)
    {
        $reminders = DB::table('donation_reminder')
                        ->join('user_donation_reminder', 'donation_reminder.id', '=', 'user_donation_reminder.reminder_id')
                        ->join('donations', 'user_donation_reminder.donation_id', '=', 'donations.id')
                        ->select('donations.id', 'donation_reminder.id as reminder_id', 'donations.nama', 'donation_reminder.date', 'donation_reminder.day', 'donation_reminder.time', 'donation_reminder.recurrence')
                        ->where('donations.id', $donationId)
                        ->orderBy('donations.nama')
                        ->get();

        return $reminders;
    }

    public static function getAllReminder()
    {
        $reminders = DB::table('donation_reminder')
                        ->join('user_donation_reminder', 'donation_reminder.id', '=', 'user_donation_reminder.reminder_id')
                        ->join('donations', 'user_donation_reminder.donation_id', '=', 'donations.id')
                        ->select('donations.id', 'donation_reminder.id as reminder_id', 'donations.nama', 'donation_reminder.date', 'donation_reminder.day', 'donation_reminder.time', 'donation_reminder.recurrence')
                        ->orderBy('donations.nama')
                        ->get();
        
        return $reminders;
    }

    public function getReminderDatatable(Request $request)
    {
        $donationId = $request->donationId;

        if (request()->ajax()) {
            if (is_null($donationId)) {
                $data = $this->getAllReminder();
            } else {
                $data = $this->getReminderByDonationId($donationId);
            }

            // dd($data);
            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('reminder.edit', $row->reminder_id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->reminder_id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->make(true);
        }
    }
}
