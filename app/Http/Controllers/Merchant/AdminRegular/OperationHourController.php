<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Models\OrganizationHours;
use App\Models\PgngOrder;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class OperationHourController extends Controller
{
    private $day_name = array('Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu');
    
    public function index()
    {
        $merchant = RegularMerchantController::getAllMerchantOrganization();

        return view('merchant.regular.admin.operation-hour.index', compact('merchant'));
    }

    public function getOperationHoursTable(Request $request)
    {
        $id = $request->id;

        $hour = OrganizationHours::where('organization_id', $id)->get();

        if(request()->ajax()) 
        { 
            $table = Datatables::of($hour);

            $table->editColumn('day', function ($row) {
                $day_name = $this->day_name;
                return $day_name[$row->day];
            });

            $table->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    $label = "<span class='badge rounded-pill bg-success text-white p-2'>Buka</span>";
                    return $label;
                } else {
                    $label = "<span class='badge rounded-pill bg-danger text-white p-2'>Tutup</span>";
                    return $label;
                }
            });

            $table->editColumn('open_time', function ($row) {
                if($row->status == 1) {
                    $open = Carbon::parse($row->open_hour)->format('h:i A');
                } else {
                    $open = '';
                }
                return $open;
            });

            $table->editColumn('close_time', function ($row) {
                if($row->status == 1) {
                    $close = Carbon::parse($row->close_hour)->format('h:i A');
                } else {
                    $close = '';
                }
                return $close;
            });

            $table->editColumn('action', function ($row) {
                $btn = '<button data-hour-id="'.$row->id.'" class="edit-time-btn btn btn-primary"><i class="fas fa-pencil-alt"></i></button>';
                return $btn;
            });

            $table->rawColumns(['day', 'status', 'open_time', 'close_time', 'action']);

            return $table->make(true);
        }
    }

    public function edit(Request $request)
    {
        $day_name = $this->day_name;
        $hour = OrganizationHours::find($request->hour_id);

        return response()->json(['hour' => $hour, 'day_name' => $day_name]);
    }

    public function update(Request $request)
    {
        // Initialize data
        $open_hour = $request->open_hour;
        $close_hour = $request->close_hour;
        $status = $request->status;
        $hour_id = $request->hour_id;
        $alert = "";
        $new_order = array();

        $hour = OrganizationHours::where('id', $hour_id);
        $get_hour = $hour->first();

        // Get all order that already paid
        $order = $this->getOrderByDay($get_hour->organization_id, $get_hour->day);
        
        // Open
        if($status == 1)
        {
            if($open_hour == '' || $close_hour == ''){
                $alert = "Sila isi waktu yang kosong";
                $response = array(
                    'alert' => $alert,
                    'status' => 'invalid-time'
                );
                return response()->json(['response' => $response]);
            }

            if($open_hour >= $close_hour)
            {
                $alert = "Waktu premis tutup mestilah tidak kurang daripada waktu pembukaan";
                $response = array(
                    'alert' => $alert,
                    'status' => 'invalid-time'
                );
                return response()->json(['response' => $response]);
            }
            else
            {
                $new_order = $this->getOrderByDate($order, $open_hour, $close_hour);
                if(count($new_order) != 0)
                {
                    $form_data = array(
                        'org_id' => $get_hour->organization_id,
                        'hour_id' => $hour_id,
                        'new_order' => $new_order,
                        'day' => $get_hour->day,
                        'status' => $status,
                        'open_hour' => $open_hour,
                        'close_hour' => $close_hour
                    );
                    
                    $body = $this->createFormForOrderExists($form_data);

                    $alert = "Terdapat pesanan yang aktif pada hari ini, sila semak pesanan tersebut sebelum membuat perubahan";
                    $response = array(
                        'alert' => $alert,
                        'status' => 'order-exist',
                        'order' => $body
                    );
                    return response()->json(['response' => $response]);
                }
                else
                {
                    // Feature idea - Delete all <In Cart> orders so it wont proceed to orders if the existing pickup_date is invalid
                    $hour->update([
                        'open_hour' => $open_hour,
                        'close_hour' => $close_hour,
                        'status' => $status,
                    ]);
                    return response()->json(['response' => 'success']);
                }
            }
        }
        else // Close
        {
            if(count($order) != 0)
            {
                $form_data = array(
                    'org_id' => $get_hour->organization_id,
                    'hour_id' => $hour_id,
                    'new_order' => $order,
                    'day' => $get_hour->day,
                    'status' => $status,
                    'open_hour' => null,
                    'close_hour' => null
                );
                $body = $this->createFormForOrderExists($form_data);

                $alert = "Terdapat pesanan yang aktif pada hari ini, sila semak pesanan tersebut sebelum membuat perubahan";
                $response = array(
                    'alert' => $alert,
                    'status' => 'order-exist',
                    'order' => $body
                );
                return response()->json(['response' => $response]);
            }
            else
            {
                // Feature idea - Delete all <In Cart> orders so it wont proceed to orders if the existing pickup_date is invalid
                $hour->update([
                    'open_hour' => null,
                    'close_hour' => null,
                    'status' => $status,
                ]);

                return response()->json(['response' => 'success']);
            }
        }
    }

    private function getOrderByDay($org_id, $choosen_day)
    {
        $same_day_order = array();

        $order = PgngOrder::where('organization_id', $org_id)
        ->where('status', 'Paid')
        ->where('order_type', 'Pick-Up')
        ->select('id', 'pickup_date')->get();

        foreach($order as $row)
        {
            $pickup_day = RegularMerchantController::getDayIntegerByDayName(Carbon::parse($row->pickup_date)->format('l'));
            if($pickup_day == $choosen_day)
            {
                $same_day_order[] = $row->id;
            }
        }

        return $same_day_order;
    }

    private function getOrderByDate($order_id, $open_hour, $close_hour)
    {
        $newId = array();
        // get all order
        $order = PgngOrder::whereIn('id', $order_id)->get();
        
        foreach($order as $row)
        {
            $start_day = Carbon::parse($row->pickup_date)->startOfDay()->toDateTimeString(); // return date but time 00:00:00
            $end_day = Carbon::parse($row->pickup_date)->endOfDay()->toDateTimeString(); // return date  but time 23:59:59
            $new_open = Carbon::parse($row->pickup_date)->toDateString().' '.$open_hour; // concat date and new updated opening hour
            $new_close = Carbon::parse($row->pickup_date)->toDateString().' '.$close_hour; // concat date and new updated closing hour

            /* If order time is outside of range from start of day to new opening hour AND outside of range from new closing hour to end of day*/
            if(($start_day <= $row->pickup_date && $new_open >= $row->pickup_date) || 
            ($new_close <= $row->pickup_date && $end_day >= $row->pickup_date))
            {
                $newId[] = $row->id;
            }
        }

        return $newId;
    }
    
    private function createFormForOrderExists($arr)
    {
        // $day = null;
        $order_id = "";
        $order_id = base64_encode(serialize($arr['new_order']));
        $body = "<form action='/admin-regular/operation-hours/".$arr['org_id']."/check-orders/".$arr['hour_id']."' method='GET'>";
        $body .= "<input type='hidden' name='order_id' value='".$order_id."'>";
        $body .= "<input type='hidden' name='day' value='".$arr['day']."'>";
        $body .= "<input type='hidden' name='status' value='".$arr['status']."'>";
        $body .= "<input type='hidden' name='new_open_hour' value='".$arr['open_hour']."'>";
        $body .= "<input type='hidden' name='new_close_hour' value='".$arr['close_hour']."'>";
        $body .= "<button type='submit' id='btn-check-order' class='btn btn-primary'>Semak Pesanan</button>";
        $body .= "</form>";
        // $body = "<a href='/admin-merchant/operation-hours/check-orders/".$hour_id."'>Semak Pesanan</a>";

        return $body;
    }

    public function editSameDateOrders(Request $request, $org_id, $hour_id)
    {
        # Unserialize variable of array
        $order_id = unserialize(base64_decode($request->order_id));
        $day = $request->day;
        $status = $request->status;
        $open = null;
        $close = null;
        $order_exists = array();
        $pickup_date = array();

        # Get all order of passed ID
        $unprocessed_order = PgngOrder::whereIn('id', $order_id)
        ->where('deleted_at', null)
        ->select('id', 'pickup_date')
        ->get();

        # Initialize open and close hour if status is OPEN
        if($status != 0) {
            $open = Carbon::parse($request->new_open_hour)->toTimeString();
            $close = Carbon::parse($request->new_close_hour)->toTimeString();
        }

        $time_arr = array(
            'hour_id' => $hour_id,
            'opening_time' => $open,
            'closing_time' => $close,
            'day' => $day,
            'status' => $status,
        );

        $order_exists = $this->getOrderExists($unprocessed_order, $time_arr);

        # Display the order that still exists
        $order = DB::table('pgng_orders as po')
        ->join('users as u', 'u.id', '=', 'po.user_id')
        ->whereIn('po.id', $order_exists)
        ->select('po.id', 'po.total_price', 'po.pickup_date', 'u.name', 'u.telno')
        ->get();
        
        foreach($order as $row)
        {
            $pickup_date[$row->id] = Carbon::parse($row->pickup_date)->format('Y-m-d h:i A');
        }
        
        return view('merchant.regular.admin.operation-hour.order', compact('order', 'pickup_date', 'time_arr'));
    }

    private function getOrderExists($order, $arr)
    {
        $order_exists = array();
        foreach($order as $row)
        {   
            # Get order pickup date day and convert to integer
            $day_pickup_date = Carbon::parse($row->pickup_date)->format('l');
            $day_int_pickup = RegularMerchantController::getDayIntegerByDayName($day_pickup_date);

            # If it is the same day as order pickup_date and the day of selected day the admin want to update
            # And if the status is OPEN
            if($arr['day'] == $day_int_pickup && $arr['status'] == 1)
            {
                $start_day = "00:00:00";
                $end_day = "23:59:59";
                $time_of_date = Carbon::parse($row->id)->toTimeString();
                
                # If the order time is out of range of new open and close time
                # Check time from start of the day until new time open and Check time from new close time until the end of the day
                if(($start_day < $time_of_date && $arr['opening_time'] > $time_of_date) || 
                ($arr['closing_time'] < $time_of_date && $end_day > $time_of_date)) 
                {
                    $order_exists[] = $row->id;
                }
            }

            # If it is the same day as order pickup_date and the day of selected day the admin want to update
            # And if the status is CLOSE
            if($arr['day'] == $day_int_pickup && $arr['status']== 0) {
                $order_exists[] = $row->id;
            }
        }

        return $order_exists;
    }

    public function changeOrderPickupDate(Request $request)
    {
        $time_arr = array(
            "updated_day" => $request->day,
            "requested_pickup_date" => $request->date_time,
            "day_status" => $request->status,
            "new_opening_time" => $request->open,
            "new_closing_time" => $request->close, 
        );

        # Validate the admin input
        $validationStatus = $this->validatePickupDate($time_arr);

        if($validationStatus != null)
        {
            return $validationStatus;
        }
        
        # Update order date
        $update_order = PgngOrder::find($request->o_id)->update([
            'pickup_date' => Carbon::parse($request->date_time)->toDateTimeString(),
        ]);

        if($update_order) {
            $msg = "Tarikh dan masa pesanan berjaya diubah ke ".Carbon::parse($request->date_time)->format('Y-m-d h:i A');
            Session::flash('success', $msg);
        } else {
            $msg = "Error . Kemaskini tidak berjaya";
            Session::flash('error', $msg);
        }
    }

    private function validatePickupDate($arr)
    {
        $msg = '';
        $validation_response = null;
        $day_date_time = Carbon::parse($arr['requested_pickup_date'])->format('l');
        $pickup_day = RegularMerchantController::getDayIntegerByDayName($day_date_time);

        if($arr['requested_pickup_date'] == ''){
            $msg = "Sila isi tarikh dan masa pengambilan baru.";
            $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
        }
        
        // If updated day same as the day of pickup date
        if($arr['updated_day'] == $pickup_day) {
            // the day is close
            if($arr['day_status'] == 0) {
                $msg = "Tidak boleh tarikh hari yang sama";
                $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
            }
            
            // the day is open
            if($arr['day_status'] == 1) {
                $pickup_time = Carbon::parse($arr['requested_pickup_date'])->toTimeString();
                
                // If the pickup time is NOT in range of opening and closing time
                if(!($arr['new_opening_time'] <= $pickup_time && $pickup_time <= $arr['new_closing_time'])) {
                    $msg = "Anda memilih hari yang sama dan masa yang dipilih di luar jurang masa buka dan tutup baharu";
                    $validation_response = response()->json(['status' => 'error', 'message' => $msg]);
                }
            }
        }
        
        // If the pickup date is less than current time
        if(Carbon::parse($arr['requested_pickup_date'])->lte(Carbon::now()))
        {
            $msg = "Masa yang dipilih melebihi masa kini";
            $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
        }

        return $validation_response;
    }

    public function updateNew(Request $request)
    {
        $update_hour = OrganizationHours::find($request->hour_id)->update([
            'status' => $request->status,
            'open_hour' => $request->updated_open,
            'close_hour' => $request->updated_close,
        ]);
        
        if($update_hour) {
            return redirect('/admin-regular/operation-hours')->with('success', 'Waktu Operasi Berjaya Dikemaskini');
        } else {
            return back()->with('error', 'Waktu Operasi Tidak Berjaya Dikemaskini');
        }
    }
}