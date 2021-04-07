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
        }
    }

    public function getTotalDonor(Request $request)
    {
        $organizationID = $request->id;
        $duration = $request->duration;

        if ($duration == "day") {
            try {
                $response = Transaction::getTotalDonorByDay($organizationID);
                $response = json_decode($response, true);
                $response['duration'] = 'day';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "week") {
            try {
                $response = Transaction::getTotalDonorByWeek($organizationID);
                $response = json_decode($response, true);
                $response['duration'] = 'week';

                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "month") {
            try {
                $response = Transaction::getTotalDonorByMonth($organizationID);
                $response = json_decode($response, true);
                $response['duration'] = 'month';
                
                return $this->sendResponse($response, "Success");
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        }
    }

    public function getLatestTransaction(Request $request)
    {
        $organizationID = $request->id;

        try {
            $response = Transaction::getLastestTransaction($organizationID);
            
            if (request()->ajax()) {
                return datatables()->of($response)
                    ->editColumn('latest', function ($response) {
                        //change over here
                        return date('d/m/Y', strtotime($response->latest));
                    })
                    ->make(true);
            }
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getTransactionByOrganizationIdAndStatus(Request $request)
    {
        $organizationID = $request->id;
        try {
            $response = Transaction::getTransaction($organizationID);

            return $this->sendResponse($response, "Success");
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }
}
