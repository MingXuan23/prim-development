<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Transaction;

class DashboardController extends AppBaseController
{
    public function getTotalDonation(Request $request)
    {
        $organizationID = $request->id;
        $duration = $request->duration;

        if ($duration == "day") {
            try {
                $response = Transaction::getTotalDonationByDay($organizationID);
                $response = json_decode($response, true);
                $response['duration'] = 'day';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "week") {
            try {
                $response = Transaction::getTotalDonationByWeek($organizationID);
                $response = json_decode($response, true);
                $response['duration'] = 'week';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "month") {
            try {
                $response = Transaction::getTotalDonationByMonth($organizationID);
                $response = json_decode($response, true);
                $response['duration'] = 'month';
                
                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } else {
        }
    }
}
