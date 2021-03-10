<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use App\Http\Controllers\DonationController;
use App\Http\Requests\ReminderRequest;
use App\Models\Donation;
use DB;
use Illuminate\Support\Carbon;

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
        return view("reminder.add");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReminderRequest $request)
    {
        $dateRequest = new Carbon($request->date);

        $date = $dateRequest->format('d');
        

        dd($date);
        // $reminder = Reminder::create([
        //     'timezoneoffset'       s    =>
        // ]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public static function getReminderByDonationId($donationId)
    {
        $reminders = DB::table('donation_reminder')
                        ->join('user_donation_reminder', 'donation_reminder.id', '=', 'user_donation_reminder.reminder_id')
                        ->join('donations', 'user_donation_reminder.donation_id', '=', 'donations.id')
                        ->select('donations.id', 'donations.nama', 'donation_reminder.date', 'donation_reminder.day', 'donation_reminder.time', 'donation_reminder.recurrence')
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
                        ->select('donations.id', 'donations.nama', 'donation_reminder.date', 'donation_reminder.day', 'donation_reminder.time', 'donation_reminder.recurrence')
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

            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('reminder.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                })
                ->make(true);
        }
    }
}
