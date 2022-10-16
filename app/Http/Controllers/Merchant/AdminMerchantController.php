<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\PickUpOrder;
use App\Models\ProductItem;
use App\Models\ProductGroup;
use App\Models\ProductOrder;
use App\Models\Queue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class AdminMerchantController extends Controller
{
    private function getOrganizationId()
    {
        $org_id = DB::table('organizations as o')
        ->join('organization_user as ou', 'ou.organization_id', '=', 'o.id')
        ->where([
            ['user_id', Auth::id()],
            ['role_id', 2015],
            ['status', 1],
            ['type_org', 2132],
            ['deleted_at', NULL],
        ])
        ->select('o.id')
        ->first()->id;
        
        return $org_id;
    }
    /* START INDEX SECTION */
    public function index()
    {
        return view('merchant.admin.index');
    }
    /* END INDEX SECTION */

    /* START OPERATION HOURS SECTION */
    public function showOperationHours()
    {
        $day_name = array('Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu');
        $org_id = $this->getOrganizationId();
        $hour = OrganizationHours::where('organization_id', $org_id)->get();

        return view('merchant.admin.operation-hour.index', compact('day_name', 'hour'));
    }

    public function editOperationHours(Request $request)
    {
        $day_name = array('Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu');
        $hour = OrganizationHours::find($request->hour_id);

        return response()->json(['hour' => $hour, 'day_name' => $day_name]);
    }

    public function updateOperationHours(Request $request)
    {
        $open = $request->open_hour;
        $close = $request->close_hour;
        $status = $request->status;
        $hour_id = $request->hour_id;
        $alert = "";
        $newOrder = array();

        $hour = OrganizationHours::where('id', $hour_id);
        $getHour = $hour->first();
        $order = $this->getOrderByDay($getHour->organization_id, $getHour->day);
        
        // Open
        if($status == 1)
        {
            if($open >= $close)
            {
                $alert = "Waktu premis tutup mestilah tidak kurang daripada waktu pembukaan";
                return response()->json(['alert' => $alert, 'status' => 'error_time']);
            }
            else
            {
                $newOrder = $this->getOrderByDate($order, $open, $close);
                if(count($newOrder) != 0)
                {
                    $alert = "Terdapat pesanan yang aktif pada hari ini, sila semak pesanan tersebut sebelum membuat perubahan";
                    $body = $this->createFormForOrderExists($hour_id, $newOrder, $getHour->day, $status, $open, $close);
                    return response()->json([
                        'alert' => $alert,
                        'status' => 'error_order',
                        'order' => $body,
                    ]);
                }
                else
                {
                    $hour->update([
                        'open_hour' => $open,
                        'close_hour' => $close,
                        'status' => $status,
                    ]);
                    return response()->json(['status' => 'success']);
                }
            }
        }
        else // Close
        {
            if(count($order) != 0)
            {
                $alert = "Terdapat pesanan yang aktif pada hari ini, sila semak pesanan tersebut sebelum membuat perubahan";
                $body = $this->createFormForOrderExists($hour_id, $order, $getHour->day, $status, null, null);
                return response()->json([
                    'alert' => $alert,
                    'status' => 'error_order',
                    'order' => $body,
                ]);
            }
            else
            {
                $hour->update([
                    'open_hour' => null,
                    'close_hour' => null,
                    'status' => $status,
                ]);
                return response()->json(['status' => 'success']);
            }
        }
    }

    private function getOrderByDay($org_id, $day)
    {
        $orderDay = array();
        $same_day_order = array();

        $order = PickUpOrder::where('organization_id', $org_id)->where('status', 2)->get();

        foreach($order as $row)
        {
            $orderDay[] = [
                'id' => $row->id,
                'day' => app('App\Http\Controllers\CooperativeController')
                         ->getDayIntegerByDayName(
                            Carbon::parse($row->pickup_date)->format('l')
                         ),
            ];
        }
        
        foreach($orderDay as $row)
        {
            if($row['day'] == $day)
            {
                $same_day_order[] = $row['id'];
            }
        }

        return $same_day_order;
    }

    private function getOrderByDate($order_id, $open_hour, $close_hour)
    {
        $newId = array();
        $order = PickUpOrder::whereIn('id', $order_id)->get();
        
        foreach($order as $row)
        {
            $start_day = Carbon::parse($row->pickup_date)->startOfDay()->toDateTimeString();
            $end_day = Carbon::parse($row->pickup_date)->endOfDay()->toDateTimeString();
            $new_open = Carbon::parse($row->pickup_date)->toDateString().' '.$open_hour;
            $new_close = Carbon::parse($row->pickup_date)->toDateString().' '.$close_hour;

            if($start_day <= $row->pickup_date && $new_open >= $row->pickup_date)
            {
                $newId[] = $row->id;
            }

            if($new_close <= $row->pickup_date && $end_day >= $row->pickup_date)
            {
                $newId[] = $row->id;
            }
        }

        return $newId;
    }
    
    private function createFormForOrderExists($hour_id, $order, $day, $status, $new_open_hour, $new_close_hour)
    {
        // $day = null;
        $order_id = "";
        $order_id = base64_encode(serialize($order));
        $body = "<form action='/admin-merchant/operation-hours/check-orders/".$hour_id."' method='GET'>";
        $body .= "<input type='hidden' name='order_id' value='".$order_id."'>";
        $body .= "<input type='hidden' name='day' value='".$day."'>";
        $body .= "<input type='hidden' name='status' value='".$status."'>";
        $body .= "<input type='hidden' name='new_open_hour' value='".$new_open_hour."'>";
        $body .= "<input type='hidden' name='new_close_hour' value='".$new_close_hour."'>";
        $body .= "<button type='submit' id='btn-check-order' class='btn btn-primary'>Semak Pesanan</button>";
        $body .= "</form>";
        // $body = "<a href='/admin-merchant/operation-hours/check-orders/".$hour_id."'>Semak Pesanan</a>";

        return $body;
    }

    public function editExistingOrder(Request $request, $id)
    {
        # Unserialize variable of array
        $order_id = unserialize(base64_decode($request->order_id));
        $day = $request->day;
        $status = $request->status;
        $open = null;
        $close = null;
        $hour_id = $id;

        # Get all order of passed ID
        $unprocessed_order = DB::table('pickup_order')
        ->whereIn('id', $order_id)
        ->where('deleted_at', null)
        ->select('id', 'pickup_date')
        ->get();
        
        $order_exists = array();
        $pickup_date = array();

        # Initialize open and close hour if status is OPEN
        if($status != 0) {
            $open = Carbon::parse($request->new_open_hour)->toTimeString();
            $close = Carbon::parse($request->new_close_hour)->toTimeString();
        }

        foreach($unprocessed_order as $row)
        {   
            # Get order pickup date day and convert to integer
            $day_pickup_date = Carbon::parse($row->pickup_date)->format('l');
            $day_int_pickup = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($day_pickup_date);

            # If it is the same day as order pickup_date and the day of selected day the admin want to update
            # And if the status is OPEN
            if($day == $day_int_pickup && $status == 1)
            {
                $start_day = "00:00:00";
                $end_day = "23:59:59";
                $time_of_date = Carbon::parse($row->id)->toTimeString();
                
                # If the order time is out of range of new open and close time
                if($start_day < $time_of_date && $open > $time_of_date) // Check time from start of the day until new time open
                {
                    $order_exists[] = $row->id;
                }

                if($close < $time_of_date && $end_day > $time_of_date) // Check time from new close time until the end of the day
                {
                    $order_exists[] = $row->id;
                }
            }

            # If it is the same day as order pickup_date and the day of selected day the admin want to update
            # And if the status is CLOSE
            if($day == $day_int_pickup && $status == 0) {
                $order_exists[] = $row->id;
            }
        }

        # Display the order that still exists
        $order = DB::table('pickup_order as po')
        ->join('users as u', 'u.id', '=', 'po.user_id')
        ->whereIn('po.id', $order_exists)
        ->select('po.id', 'po.total_price', 'po.pickup_date', 'u.name', 'u.telno')
        ->get();
        
        
        foreach($order as $row)
        {
            $pickup_date[$row->id] = Carbon::parse($row->pickup_date)->format('Y-m-d h:i A');
        }
        
        return view('merchant.admin.operation-hour.order', compact('order', 'pickup_date', 'day', 'status', 'open', 'close', 'hour_id'));
    }

    public function changeOrderExistDateTime(Request $request)
    {
        $order_id = $request->o_id;
        $date_time = $request->date_time;
        $day_select = $request->day;
        $status = $request->status;

        # Validate the admin input
        $validationStatus = $this->validateChangeDateTime(
            $day_select, $date_time, $status, $request->open, $request->close
        );

        if($validationStatus != null)
        {
            return $validationStatus;
        }
        
        $user_order = PickUpOrder::find($order_id)
        ->select(
            'pickup_date', 'organization_id',
        )
        ->first();

        $pickup_date = $user_order->pickup_date;
        $org_id = $user_order->organization_id;
        $old_date = Carbon::parse($pickup_date)->toDateString();
        $new_date = Carbon::parse($date_time)->toDateString();

        $cart = DB::table('product_order')
        ->where('pickup_order_id', $order_id)
        ->select(
            'id', 'quantity', 'product_item_id',
        )->get();
        
        # Check if the date are the same or not
        $e_pqueue = $this->checkProductQueueSameDate($old_date, $new_date, $cart);

        foreach($cart as $row)
        {
            $item = ProductItem::find($row->product_item_id);

            # Get all order that are on the same date
            $all_order[$row->id] = app('App\Http\Controllers\Merchant\MerchantController')
            ->checkDateForQueue($date_time, $org_id, $row->product_item_id);
            
            # Get queue id based on the change date time
            $queue[$row->id] = $this->getQueueArray($date_time, $org_id, $item->product_group_id);

            # Check if there is queue slot time available
            if(count($queue[$row->id]) != 0) // Available Queue
            {
                # Generate Product Queue based on available Queue
                $product_queue[$row->id] = $this->getProductQueueArray(
                    $queue[$row->id], $row->quantity, $all_order[$row->id], $item->quantity, $row->id);
                
                # Check if all product queue data successfully generated
                if($product_queue[$row->id] == 0) // Unsuccessful
                {
                    # If the date requested is on the same date
                    if($old_date == $new_date)
                    {
                        # Re-insert the data
                        foreach($e_pqueue as $row_insert)
                        {
                            DB::table('product_queue')->insert($row_insert);
                        }
                    }
                    $msg = "Kuantiti item melebihi slot yang ada, Sila pilih tarikh atau masa yang lain";
                    return response()->json(['status' => 'error', 'message' => $msg]); 
                }
            }
            else // Unavailable Queue
            {
                $msg = "Tiada slot untuk tarikh dan masa ini";
                return response()->json(['status' => 'error', 'message' => $msg]); 
            }
        }

        # Get all the order product queue
        $existing_product_queue = $this->getSelectedOrderProductQueue($cart);

        # Delete all old order product queue
        foreach($existing_product_queue as $row)
        {
            DB::table('product_queue')->where('id', $row->id)->delete();
        }

        # insert all new order product queue
        foreach($product_queue as $row)
        {
            DB::table('product_queue')->insert($row);
        }

        # Update order date
        PickUpOrder::find($order_id)->update([
            'pickup_date' => Carbon::parse($date_time)->toDateTimeString(),
        ]);

        $msg = "Tarikh dan masa pesanan berjaya diubah ke ".Carbon::parse($date_time)->format('Y-m-d h:i A');
        Session::flash('success', $msg);
    }

    private function validateChangeDateTime($day_selected, $date_time_selected, $day_status, $new_open_hour, $new_close_hour)
    {
        $msg = '';
        $validation_response = null;
        $time_now = Carbon::now();
        $day_date_time = Carbon::parse($date_time_selected)->format('l');
        $day_change = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($day_date_time);
        
        if($day_selected == $day_change && $day_status == 0) {
            $msg = "Tidak boleh tarikh hari yang sama";
            $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
        }

        if($day_selected == $day_change && $day_status == 1) {
            $start_day = "00:00:00";
            $end_day = "23:59:59";
            $time_of_date = Carbon::parse($date_time_selected)->toTimeString();

            if($start_day < $time_of_date && $new_open_hour > $time_of_date)
            {
                $msg = "Anda memilih hari yang sama dan masa yang dipilih di luar jurang masa buka dan tutup baharu";
                $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
            }

            if($new_close_hour < $time_of_date && $end_day > $time_of_date)
            {
                $msg = "Anda memilih hari yang sama dan masa yang dipilih di luar jurang masa buka dan tutup baharu";
                $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
            }
        }

        if(Carbon::parse($date_time_selected)->lte($time_now))
        {
            $msg = "Masa yang dipilih melebihi masa kini";
            $validation_response = response()->json(['status' => 'error', 'message' => $msg]); 
        }

        return $validation_response;
    }

    private function checkProductQueueSameDate($old_date, $new_date, $cart_arr)
    {
        $e_pqueue = array();

        $existing_product_queue = $this->getSelectedOrderProductQueue($cart_arr);

        if($old_date == $new_date)
        {
            foreach($existing_product_queue as $row)
            {
                $e_pqueue[] = [
                    'quantity_slot' => $row->quantity_slot,
                    'product_order_id' => $row->product_order_id,
                    'queue_id' => $row->queue_id,
                ];

                DB::table('product_queue')->where('product_order_id', $row->id)->delete();
            }
        }

        return $e_pqueue;
    }

    private function getSelectedOrderProductQueue($cart_arr)
    {
        foreach($cart_arr as $row)
        {
            $existing_product_queue = DB::table('product_queue')->where('product_order_id', $row->id)->get();
        }

        return $existing_product_queue;
    }

    private function getQueueArray($date_time, $organization_id, $product_group_id)
    {
        $day_date_time = Carbon::parse($date_time)->format('l');
        $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($day_date_time);
        $hour = OrganizationHours::where('organization_id', $organization_id)->where('day', $day)->first();

        $today_date = Carbon::now()->toDateString();
        $today_time = Carbon::now()->toTimeString();

        $date_pick_up = Carbon::parse($date_time)->toDateString();
        $time_pick_up = Carbon::parse($date_time)->toTimeString();

        $queue = Queue::where([
            ['product_group_id', $product_group_id],
            ['slot_time', '<=', $time_pick_up], // Max
        ])
        ->orderBy('id', 'desc')
        ->get();
        
        if($date_pick_up == $today_date && $hour->status == 1)
        {
            $queue = Queue::where([
                ['product_group_id', $product_group_id],
                ['slot_time', '>=', $today_time], // Min
                ['slot_time', '<=', $time_pick_up], // Max
            ])
            ->orderBy('id', 'desc')
            ->get();
        }

        return $queue;
    }

    private function getProductQueueArray($queue_arr, $user_quantity, $order_id, $MAX_QUANTITY, $product_order_id)
    {
        $remaining_quantity = (int)$user_quantity;
        $pq_data = array();
        # check every queue available that less than pickup time
        foreach($queue_arr as $row)
        {
            if($remaining_quantity <= 0) { break; } // If remaining quantity after calc is no more

            $quantity_inside_table = 0; // Reset value

            #get all order available based on queue id
            $product_queue = DB::table('product_queue')
            ->whereIn('product_order_id', $order_id)
            ->where('queue_id', $row->id)
            ->get();

            # get total of item quantity
            
            foreach($product_queue as $pq_row)
            {
                $quantity_inside_table += $pq_row->quantity_slot; // Example = 14
            }
            
            # Check if this slot is full or not
            if($quantity_inside_table < $MAX_QUANTITY) // NOT FULL
            {
                # Get slot available for this slot because this slot is not full yet
                $slot_availability = $MAX_QUANTITY - $quantity_inside_table; // Example = 15 - 1 = 14
                
                # Check if quantity inputted is more than slot available -> Example = 15 > 14
                if($remaining_quantity > $slot_availability)
                {
                    $pq_data[] = [
                        'quantity_slot' => $slot_availability, // Example = 14 -> Insert
                        'queue_id' => $row->id,
                        'product_order_id' => $product_order_id,
                    ];
                    $remaining_quantity -= $slot_availability; // Example = 15 - 14 = 1
                }
                else // NEXT ITERATION
                {
                    $pq_data[] = [
                        'quantity_slot' => $remaining_quantity, // Example = 1 -> INSERT
                        'queue_id' => $row->id,
                        'product_order_id' => $product_order_id,
                    ];
                    $remaining_quantity -= $slot_availability; // Example = 1 - 15 = -14 -> BREAK LOOP
                }
            }
        }
        
        if($remaining_quantity > 0) {
            return 0;
        } else {
            return $pq_data;
        }
    }

    public function updateNewOperationHours(Request $request)
    {
        $update_hour = OrganizationHours::find($request->hour_id)->update([
            'status' => $request->status,
            'open_hour' => $request->updated_open,
            'close_hour' => $request->updated_close,
        ]);

        if($update_hour) {
            return redirect('/admin-merchant/operation-hours')->with('success', 'Waktu Operasi Berjaya Dikemaskini');
        } else {
            return back()->with('error', 'Waktu Operasi Tidak Berjaya Dikemaskini');
        }
    }
    /* END OPERATION HOURS SECTION */

    /* START PRODUCT DASHBOARD SECTION */
    public function showProductDashboard()
    {
        $org_id = $this->getOrganizationId();

        $group = ProductGroup::where('organization_id', $org_id)->get();

        return view('merchant.admin.product.index', compact('group'));
    }

    public function storeProductGroup(Request $request)
    {
        $org_id = $this->getOrganizationId();
        # Insert Product Group Data
        $pg = ProductGroup::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'organization_id' => $org_id,
        ]);

        $queue = $this->getQueueByMinutes($pg);
        
        # Insert in Queue Table
        Queue::insert($queue);

        return back()->with('success', 'Jenis Produk Berjaya Direkodkan');
    }

    private function getQueueByMinutes($product_group)
    {
        $start_day = Carbon::now()->startOfDay()->toTimeString();
        $total_minutes = 1440; // total minutes in a day
        $duration = $product_group->duration;
        $temp = Carbon::parse($start_day);
        $data = array(0 => ["slot_time" => $start_day, "status" => 1, "slot_number" => 1, "product_group_id" => $product_group->id, "created_at" => Carbon::now(), "updated_at" => Carbon::now()]); // Initialize first row of queue (opening hour/ Min)
        # Loop until the total duration more or equal to total minutes
        for($newDuration = $duration, $i = 2; $newDuration < $total_minutes; $newDuration += $duration, $i++)
        {
            $temp_f = $temp->addMinutes($duration)->format('H:i:s');  // Add minutes to current time var
            $data[] = [
                "slot_time" => $temp_f,
                "status" => 1,
                "slot_number" => $i,
                "product_group_id" => $product_group->id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
            $temp = Carbon::parse($temp_f); // Reformat time so it can perform addMinutes method
        }

        return $data;
    }

    public function showProductItem($id)
    {
        $group = ProductGroup::find($id);
        $item = ProductItem::where('product_group_id', $id)->get();

        $org_name = DB::table('organizations as o')
        ->join('organization_user as ou', 'ou.organization_id', '=', 'o.id')
        ->where([
            ['user_id', Auth::id()], 
            ['role_id', 2015],
            ['status', 1],
            ['type_org', 2132],
            ['deleted_at', NULL],
        ])
        ->select('nama')->first()->nama;
        
        $image_url = "merchant-image/product-item/".$org_name."/";
        
        return view('merchant.admin.product.show', compact('item', 'group', 'image_url'));
    }

    public function updateProductGroup(Request $request)
    {
        $group = ProductGroup::where('id', $request->group_id);

        $getGroup = $group->first();
        
        $group->update([
            'name' => $request->name,
            'duration' => $request->duration,
        ]);

        if($getGroup->duration != (int)$request->duration)
        {
            Queue::where('product_group_id', $request->group_id)->delete();
            $queue = $this->getQueueByMinutes($getGroup->refresh(), $getGroup->organization_id);
            Queue::insert($queue);
        }

        return back()->with('success', 'Jenis Produk Berjaya Dikemaskini');
    }

    public function destroyProductGroup(Request $request)
    {
        $item = DB::table('product_item')
        ->where('product_group_id', $request->group_id)->get();

        foreach($item as $row)
        {
            if($row->image != NULL)
            {
                $file = public_path($request->image_url.$row->image);
                $exists = File::exists($file);
                
                if($exists)
                {
                    File::delete($file);
                }
            }

            DB::table('product_item')->where('id',$row->id)->update([
                'status' => 0,
                'deleted_at' => Carbon::now()->toDateTimeString(),
            ]);
        }

        ProductGroup::find($request->group_id)->delete();
        DB::table('queues')->where('product_group_id', $request->group_id)->delete();

        return redirect('/admin-merchant/product-dashboard')->with('success', 'Jenis Produk Berjaya Dibuang');
    }

    public function storeProductItem(Request $request)
    {
        $item = ProductItem::where('product_group_id', $request->group_id)->select('name')->get();

        foreach($item as $row)
        {
            if(strtolower($row->name) == strtolower($request->item_name))
            {
                return back()->with('error', 'Item sudah wujud dalam kumpulan ini');
            }
        }

        $link = explode(" ", $request->item_name);
        $str = implode("-", $link);
        $file_name = NULL;

        $org_name = DB::table('organizations as o')
        ->join('organization_user as ou', 'ou.organization_id', '=', 'o.id')
        ->where([
            ['user_id', Auth::id()], 
            ['role_id', 2015],
            ['status', 1],
            ['type_org', 2132],
            ['deleted_at', NULL],
        ])
        ->select('nama')->first()->nama; 
        
        if (!is_null($request->item_image)) {
            $extension = $request->item_image->extension();
            $storagePath  = $request->item_image->move(public_path('merchant-image/product-item/'.$org_name), $str.'.'.$extension);
            $file_name = basename($storagePath);
        }
        
        ProductItem::create([
            'name' => $request->item_name,
            'desc' => $request->item_desc,
            'quantity' => $request->item_quantity,
            'price' => $request->item_price,
            'image' => $file_name,
            'status' => 1,
            'product_group_id' => $request->group_id,
        ]);

        return back()->with('success', 'Item Baru Berjaya Direkodkan');
    }

    public function editProductItem(Request $request, $id, $item_id)
    {
        $item = ProductItem::find($item_id);
        $group = ProductGroup::find($id);
        $org_id = $group->organization_id;
        $org_name = Organization::find($org_id)->nama;

        $image_url = "merchant-image/product-item/".$org_name."/";

        return view('merchant.admin.product.edit', compact('item', 'image_url', 'group'));
    }

    public function updateProductItem(Request $request)
    {
        $link = explode(" ", $request->name);
        $str = implode("-", $link);

        $file_name = NULL;

        $item = ProductItem::find($request->id);

        if(!is_null($item->image))
        {
            $file_name = $item->image;
        }
        
        if (!is_null($request->image)) {
            $file = public_path($request->image_url.$item->image);
            $exists = File::exists($file);

            if($exists)
            {
                File::delete($file);
            }
            
            $extension = $request->image->extension();
            $storagePath  = $request->image->move(public_path($request->image_url), $str.'.'.$extension);
            $file_name = basename($storagePath);
        }

        ProductItem::where('id', $request->id)->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'quantity' => $request->quantity,
            'price' => number_format($request->price, 2, '.', ''),
            'image' => $file_name,
        ]);

        return back()->with('success', 'Berjaya dikemaskini');
    }

    public function getItemForDestroy(Request $request)
    {
        $item_name = ProductItem::find($request->i_id)->name;
        $body = "Adakah anda pasti mahu buang <strong>".$item_name."</strong>?";
        return response()->json(['body' => $body]);
    }

    public function destoryProductItem(Request $request)
    {
        $item = DB::table('product_item')->where('id', $request->i_id);

        $item_image = $item->first()->image;

        if($item_image != NULL)
        {
            $file = public_path($request->image_url.$item_image);
            $exists = File::exists($file);
            
            if($exists)
            {
                File::delete($file);
            }
        }
        
        $item->update(['status' => 1, 'deleted_at' => Carbon::now()->toDateTimeString()]);
    }

    /* END PRODUCT DASHBOARD SECTION */

    /* START ORDER SECTION */
    public function showAllOrder()
    {
        return view('merchant.admin.order.index');
    }

    public function getAllOrder(Request $request)
    {
        $org_id = $this->getOrganizationId();
        $total_price[] = 0;
        $pickup_date[] = 0;
        $filteredID = array();
        $order_day = $request->order_day;

        $order = DB::table('pickup_order as pu')
                ->join('users as u', 'pu.user_id', '=', 'u.id')
                ->whereIn('status', [2,4])
                ->where('organization_id', $org_id)
                ->select('pu.id', 'pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                'u.name', 'u.telno')
                ->orderBy('status', 'desc')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->get();
        
        if(request()->ajax()) 
        {
            if($order_day == "") 
            {
                $order = $order;
            }
            else
            {
                foreach($order as $row) {
                    $day_pickup = Carbon::parse($row->pickup_date)->format('l');
                    $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($day_pickup);
                    if($day == $order_day) {
                        $filteredID[] = $row->id;
                    }
                }

                $order = DB::table('pickup_order as pu')
                ->join('users as u', 'pu.user_id', '=', 'u.id')
                ->whereIn('pu.id', $filteredID)
                ->select('pu.id', 'pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                'u.name', 'u.telno')
                ->orderBy('status', 'desc')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc');
            }

            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == 2) {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>';
                    return $btn;
                } else {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>';
                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $btn = '<div class="d-flex justify-content-center align-items-center">';
                $btn = $btn.'<button type="button" class="btn-done-pickup btn btn-primary mr-2" data-order-id="'.$row->id.'"><i class="fas fa-clipboard-check"></i></button>';
                $btn = $btn.'<button type="button" class="btn-cancel-order btn btn-danger" data-order-id="'.$row->id.'">';
                $btn = $btn.'<i class="fas fa-trash-alt"></i></button></div>';

                return $btn;
            });

            $table->editColumn('note', function ($row) {
                if($row->note != null) {
                    return $row->note;
                } else {
                    return "<i>Tiada Nota</i>";
                }
                return number_format($row->total_price, 2, '.', '');
            });

            $table->editColumn('total_price', function ($row) {
                $total_price = number_format($row->total_price, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('admin.merchant.list', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
            });

            $table->rawColumns(['note', 'total_price', 'status', 'action']);

            return $table->make(true);
        }
    }

    public function confirmOrder(Request $request)
    {
        $order_id = $request->o_id;
        $update_order = PickUpOrder::find($order_id)->update(['status' => 3]);
        $update_cart = ProductOrder::where('pickup_order_id', $order_id)->update(['status' => 3]);

        if ($update_order && $update_cart) {
            Session::flash('success', 'Pesanan Berjaya Diambil');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Disahkan');
            return View::make('layouts/flash-messages');
        }
    }

    public function showHistoryOrder()
    {
        return view('merchant.admin.order.history');
    }

    public function getAllHistory(Request $request)
    {
        $org_id = $this->getOrganizationId();
        $total_price[] = 0;
        $pickup_date[] = 0;
        $filteredID = array();
        $order_day = $request->order_day;

        $order = DB::table('pickup_order as pu')
                ->join('users as u', 'pu.user_id', '=', 'u.id')
                ->whereIn('status', [3,100, 200])
                ->where('organization_id', $org_id)
                ->select('pu.id', 'pu.pickup_date', 'pu.total_price', 'pu.status',
                'u.name', 'u.telno')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->get();
        
        if(request()->ajax()) 
        {
            if($order_day == "") 
            {
                $order = $order;
            }
            else
            {
                foreach($order as $row) {
                    $day_pickup = Carbon::parse($row->pickup_date)->format('l');
                    $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($day_pickup);
                    if($day == $order_day) {
                        $filteredID[] = $row->id;
                    }
                }

                $order = DB::table('pickup_order as pu')
                ->join('users as u', 'pu.user_id', '=', 'u.id')
                ->whereIn('pu.id', $filteredID)
                ->select('pu.id', 'pu.pickup_date', 'pu.total_price', 'pu.status',
                'u.name', 'u.telno')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc');
            }

            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == 3) {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya diambil</span>';
                    return $btn;
                } else if($row->status == 100) {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Dibatalkan</span>';
                    return $btn;
                } else if($row->status == 200) {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Dibatalkan</span>';
                    return $btn;
                }
            });

            $table->editColumn('total_price', function ($row) {
                $total_price = number_format($row->total_price, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('admin.merchant.list', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
            });

            $table->rawColumns(['total_price', 'status']);

            return $table->make(true);
        }
    }
    /* END ORDER SECTION */

    public function destroyOrder(Request $request)
    {
        $id = $request->o_id;

        $order = PickUpOrder::find($id);
        $order->update(['status' => 100]);
        $update_order = $order->delete();
        
        $cart = ProductOrder::where('pickup_order_id', $id);
        $getCartID = $cart->select('id')->get();

        $po_id = array();

        foreach($getCartID as $row)
        {
            $po_id[] = $row->id;
        }
        
        $delete_pq = DB::table('product_queue')->whereIn('product_order_id', $po_id)->delete();

        $cart->update(['status' => 100]);
        $update_cart = $cart->delete();
        
        if($update_order && $delete_pq && $update_cart) {
            Session::flash('success', 'Pesanan Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    public function showList($id)
    {
        // Get Information about the order
        $list = DB::table('pickup_order as pu')
                ->join('users as u', 'u.id', '=', 'pu.user_id')
                ->where('pu.id', $id)
                ->where('pu.status', '>' , 0)
                ->select('pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                        'u.name', 'u.telno', 'u.email')
                ->first();

        $order_date = Carbon::parse($list->updated_at)->format('d/m/y H:i A');
        $pickup_date = Carbon::parse($list->pickup_date)->format('d/m/y H:i A');
        $total_order_price = number_format($list->total_price, 2, '.', '');

        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                ->where('po.pickup_order_id', $id)
                ->select('po.id', 'pi.name', 'pi.price', 'po.quantity')
                ->get();

        $total_price[] = array();
        $price[] = array();
        
        foreach($item as $row)
        {
            $price[$row->id] = number_format($row->price, 2, '.', '');
            $total_price[$row->id] = number_format(doubleval($row->price * $row->quantity), 2, '.', ''); // calculate total for each item in cart
        }

        return view('merchant.admin.list', compact('list', 'order_date', 'pickup_date', 'total_order_price', 'item', 'price', 'total_price'));
    }
}
