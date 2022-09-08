<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\PickUpOrder;
use App\Models\ProductItem;
use App\Models\ProductGroup;
use App\Models\ProductOrder;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;

class MerchantController extends Controller
{
    public function merchantList()
    {
        $todayDate = Carbon::now()->format('l');

        $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($todayDate);

        $merchant = Organization::with(['organization_hours' => function($q) use ($day){
            $q->where('organization_hours.day', $day);
        }])
        ->where('type_org', 2132)
        ->get();
        
        foreach($merchant as $row)
        {
            $oh_status[$row->id] = $row->organization_hours->first()->status;
        }

        return view('merchant.index', compact('merchant', 'oh_status'));
    }

    public function fetchDay(Request $request)
    {
        $user_id = Auth::id();
        $org_id = $request->get('o_id');
        $day_body = "";
        $order_id = "";

        $query = PickUpOrder::where([['user_id', $user_id], ['organization_id', $org_id], ['status', 1]]);
        $allDay = OrganizationHours::where('organization_id', $org_id)->where('status', 1)->get(); // Get all open day organization
        
        $exist = $query->exists();
        
        # If Order already exists for this organization
        if(!$exist)
        {
            $day_body = $this->getDayBody($allDay);
        }
        else
        {
            $order = $query->first();
            $order_id = $order->id;

            $isDelete = $this->deleteExpiredOrder($order_id);

            if($isDelete)
            {
                $day_body = $this->getDayBody($allDay);
            }
        }
        return response()->json(['order_id' => $order_id, 'path' => '/merchant/'.$org_id, 'day_body' => $day_body]);
    }

    private function getDayBody($all_open_days)
    {
        $dayNameMY = array("Ahad", "Isnin", "Selasa", "Rabu", "Khamis", "Jumaat", "Sabtu");
        $dayNameEN = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

        $body = "";

        $isPast = array();
        
        foreach($all_open_days as $row)
        {
            $TodayDay = Carbon::now()->format('l'); // Get Day Name

            $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($TodayDay); // Return integer based on name

            $key = strval($row->day);

            $isPast = app('App\Http\Controllers\CooperativeController')->getDayStatus($day, $row->day, $isPast, $key); // Check and return day is past or this week
        }

        foreach($all_open_days as $row)
        {
            if($TodayDay == $dayNameEN[$row->day]) // If day array is Today
            {
                $body .= "<option value='".$row->day."'>Hari ini</option>";
            }
            else
            {
                $body .= "<option value='".$row->day."'>". $dayNameMY[$row->day]." ".$isPast[$row->day]."</option>";
            }
        }

        return $body;
    }

    private function deleteExpiredOrder($id)
    {
        $order = DB::table('pickup_order')->where('id', $id);
        $getOrder = $order->first();
        
        $expiredDate_pickup = Carbon::now()->subHours(2)->toDateTimeString();
        $expiredDate_created = Carbon::now()->subDay()->toDateTimeString();

        if($getOrder->pickup_date <= $expiredDate_pickup || $getOrder->created_at <= $expiredDate_created)
        {
            $order->delete();
            return 1; // deleted
        }

        return 0; // not delete
    }

    public function fetchTime(Request $request)
    {
        $org_id = $request->get('o_id');
        $daySelected = $request->get('day');

        $Today = Carbon::now();
        
        $TodayDay = $Today->format('l');

        $TodayDayInt = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($TodayDay);

        $op_hour = OrganizationHours::where('organization_id', $org_id)
                    ->where('day', $daySelected)
                    ->select('open_hour', 'close_hour')
                    ->first();

        $min = Carbon::parse($op_hour->open_hour)->format("H:i");
        $max = Carbon::parse($op_hour->close_hour)->format("H:i");

        $alert = "";
        $btn = "";
        
        if($daySelected == $TodayDayInt) // If day selected in select option is today
        {
            $min_required_time = $Today->addHour(2)->toTimeString(); // Add 2 hour to current time

            // check if current time(after add) is between open and close hour
            if($min <= $min_required_time && $max >= $min_required_time)
            {
                $min = Carbon::parse($min_required_time)->hour; // Format the time and get hour
                $min .= ":00"; // Ex. = if above $min = 11, this return 11:00 (formatted for HTML)
            }
            else
            {
                $alert = "<input class='form-control' type='time' id='pickup_time' disabled>";
                $alert .= "<div class='alert alert-warning mt-2'><strong>Tutup:</strong> Sila pilih hari lain</div>";
            }
        }

        // Formatted for display
        $min_f = Carbon::parse($min)->format("g:i A");
        $max_f = Carbon::parse($max)->format("g:i A");

        $time_body = "<input class='form-control' type='time' min='".$min."' max='".$max."' id='pickup_time' required>";
        $time_body .= "<p>".$min_f." - ".$max_f."</p>";

        return response()->json(['time_body' => $time_body, 'alert' => $alert, 'btn' => $btn]);
    }

    public function storeOrderDate(Request $request)
    {
        $user_id = Auth::id();

        $day = $request->get('day');
        $min = $request->get('min');
        $max = $request->get('max');
        $time = $request->get('time');
        $o_id = $request->get('org_id');

        if($min <= $time && $max >= $time)
        {
            $date = $this->getPickUpDate((int)$day, $time);

            PickUpOrder::create([
                'pickup_date' => $date,
                'status' => 1,
                'user_id' => $user_id,
                'organization_id' => $o_id,
            ]);

            return response()->json(['path' => '/merchant/'.$o_id]);
        }
        else
        {
            return response()->json(['alert' => 'Sila tetapkan masa pesanan dalam jarak masa yang ditetapkan']);
        }
        
    }

    private function getPickUpDate($daySelect, $timeSelect)
    {
        $todayDate = Carbon::now()->format('l');

        $dayInt = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($todayDate);

        $date = Carbon::now()->toDateString();
        
        # If today is not 
        if($daySelect != $dayInt)
        {
            $date = Carbon::now()->next($daySelect)->toDateString();
        }

        $pickUp = $date.' '.$timeSelect.':00';

        return $pickUp;
    }

    public function destroyOldOrder(Request $request)
    {
        $id = $request->get('order_id');
        
        PickUpOrder::find($id)->forceDelete();
    }

    public function showMerchant($id)
    {
        $user_id = Auth::id();

        # <Start> Get Data for Organization
        $todayDate = Carbon::now()->format('l');

        $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($todayDate);

        $merchant = Organization::with(['organization_hours' => function($q) use ($day){
            $q->where('organization_hours.day', $day);
        }])
        ->where('id', $id)
        ->first();

        $oh = $merchant->organization_hours->first();

        $open_hour = date('h:i A', strtotime($oh->open_hour));
        
        $close_hour = date('h:i A', strtotime($oh->close_hour));
        # <End> Get Data for Organization
        
        // $order = PickUpOrder::where([
        //     ['user_id', $user_id],
        //     ['organization_id', $id],
        //     ['status', 1],
        // ])->first();

        // $pickup_time = Carbon::parse($order->pickup_date)->toTimeString();

        $product_item = DB::table('product_item as pi')
        ->join('product_group as pg', 'pg.id', '=', 'pi.product_group_id')
        ->where([
            ['pg.organization_id', $id],
            ['pg.deleted_at', NULL],
            ['pi.deleted_at', NULL],
            // ['q.slot_time', '>=', $pickup_time]
        ])
        ->select('pi.id', 'pi.name as item_name', 'pi.desc', 'pi.price', 'pi.image', 'pi.status', 'pi.product_group_id')
        ->orderBy('pi.product_group_id', 'asc')
        ->orderBy('item_name')  
        ->get();

        
        // $product_item = ProductItem::with(['product_group' => function($q) use ($id){
        //     $q->where('product_group.organization_id', $id);
        // }])
        // ->orderBy('product_group_id', 'asc')
        // ->orderBy('name')
        // ->get();

        $product_type = ProductGroup::where('organization_id', $id)
        ->orderBy('id', 'asc')
        ->get();
        
        $product_price = array();
        $temp = array();
        $jenis = array();
        foreach($product_item as $item)
        {
            foreach($product_type as $type)
            {
                if($item->product_group_id == $type->id)
                {
                    $temp[] = [
                        'type_id' => strval($type->id),
                        'type_name' => $type->name,
                    ];
                    
                    $product_price[$item->id] = number_format((double)$item->price, 2, '.', '') ;
                }
            }
        }
        $jenis = array_unique($temp, SORT_REGULAR);

        // dd($jenis);

        return view('merchant.show', compact('merchant', 'oh', 'product_item', 'open_hour', 'close_hour', 'jenis', 'product_price'));
    }

    public function fetchItem(Request $request)
    {
        $i_id = $request->get('i_id');
        $o_id = $request->get('o_id');
        
        $item = ProductItem::where('id', $i_id)
        ->select('id', 'name', 'price', 'quantity', 'status')
        ->first();

        $maxQuantity = $this->getMaxQuantityBySlotTime($i_id, $o_id);
        
        $modal = '<input id="quantity_input" type="text" value="1" name="quantity_input">';

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $maxQuantity]);
    }

    private function getMaxQuantityBySlotTime($item_id, $org_id)
    {
        $user_id = Auth::id();

        $order = PickUpOrder::where([
            ['user_id', $user_id],
            ['organization_id', $org_id],
            ['status', 1],
        ])->first();

        $min_open_day = $this->getDayOrder($order->pickup_date);
        
        $pickup_time = Carbon::parse($order->pickup_date)->toTimeString();
            
        $item = ProductItem::find($item_id);

        $queueCount = Queue::where('product_group_id', $item->product_group_id)
        ->where('slot_time', '>=', $min_open_day)
        ->where('slot_time', '<=', $pickup_time)
        ->count();

        $i_quantity = $item->quantity;
        $maxQuantity = $i_quantity * $queueCount;

        return $maxQuantity;
    }

    private function getDayOrder($dateOrder)
    {
        $todayDate = Carbon::parse($dateOrder)->format('l');
        $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($todayDate);
        $min_open_day = OrganizationHours::where('day', $day)->first()->open_hour;

        return $min_open_day;
    }

    public function storeItem(Request $request)
    {
        # Data
        $msg = '';
        $i_id = $request->get('i_id');
        $o_id = $request->get('o_id');
        $quantity = $request->get('quantity');
        $userID = Auth::id();

        # Get user order
        $order = PickUpOrder::where([
            ['user_id', $userID],
            ['organization_id', $o_id],
            ['status', 1],
        ])->first();

        $cart = ProductOrder::where([
            ['status', 1],
            ['product_item_id', $i_id],
            ['pickup_order_id', $order->id],
        ]);

        $cartExist = $cart->exists();

        # If cart exists assigned product_order_id
        if($cartExist)
        {
            $p_o_id = $cart->first()->id;
            $p_queue = DB::table('product_queue')->where('product_order_id', $p_o_id);
            # If product_queue of user order is exists -> delete data to replace
            if($p_queue->exists())
            {
                $p_queue->delete();
            }
        }

        $userOrderDateTime = $order->pickup_date;

        # Get ID for all order of the same day
        $allItem = $this->checkDateForQueue($userOrderDateTime, $o_id, $i_id);

        $MAX_ITEM_QUANTITY = ProductItem::find($i_id)->quantity;

        # Get queue available based on day and time
        $queue = $this->getQueueData($userOrderDateTime);

        if(count($queue) != 0)
        {
            # Get data for product_queue
            $pq_data = $this->getProductQueueData($queue, $quantity, $allItem, $MAX_ITEM_QUANTITY);

            if($pq_data == 0) {
                $msg = 'Harap maaf, kuantiti yang dimasukkan melebihi kekosongan yang ada';
                return response()->json(['alert' => $msg]);
            }
            else
            {
                if($cartExist)
                {
                    $cart->update(['quantity' => $quantity,]);
                }
                else
                {
                    $newOrder = ProductOrder::create([
                        'quantity' => $quantity,
                        'status' => 1,
                        'product_item_id' => $i_id,
                        'pickup_order_id' => $order->id,
                    ]);
    
                    $p_o_id = $newOrder->id;
                }
                
                # merge product_queue data with product_order_id
                for($j = 0; $j < count($pq_data); $j++)
                {
                    $data[] = array_merge($pq_data[$j], array("product_order_id" => $p_o_id));
                }
                
                $item_price = ProductItem::find($i_id)->price;

                if($order->total_price != null)
                {
                    $cal_total_price = $this->calculateTotalPrice($order->id);
                } else {
                    $cal_total_price = $item_price * $quantity;
                }

                $total_price = number_format($cal_total_price, 2, '.', '');
                
                PickUpOrder::find($order->id)->update(['total_price' => $total_price]);
                
                DB::table('product_queue')->insert($data);
                
                return response()->json(['success' => 'Item Berjaya Direkodkan', 'alert' => $msg]);
            }
        }
        else {
            $msg = 'Harap maaf, kuantiti yang dimasukkan melebihi kekosongan yang ada';
            return response()->json(['alert' => $msg]);
        }
    }

    private function checkDateForQueue($order_datetime, $organization_id, $item_id)
    {
        $startDaySelect = Carbon::parse($order_datetime)->startOfDay()->format('Y-m-d H:i:s');
        $endDaySelect = Carbon::parse($order_datetime)->endOfDay()->format('Y-m-d H:i:s');
        
        $allOrder = PickUpOrder::join('product_order', 'product_order.pickup_order_id', '=', 'pickup_order.id')
        ->where([
            ['pickup_date', '>=', $startDaySelect],
            ['pickup_date', '<=', $endDaySelect],
            ['organization_id', $organization_id],
            ['product_item_id', $item_id],
        ])
        ->whereIn('pickup_order.status', [1, 2, 3])
        ->pluck('product_order.id')
        ->toArray();

        return $allOrder;
    }

    private function getQueueData($order_datetime)
    {
        $min_open_day = $this->getDayOrder($order_datetime);

        $today_date = Carbon::now()->toDateString();
        $today_time = Carbon::now()->minute(0)->second(0)->addHour()->toTimeString();

        $date_pick_up = Carbon::parse($order_datetime)->toDateString();
        $time_pick_up = Carbon::parse($order_datetime)->toTimeString();

        if($date_pick_up != $today_date) {
            $queue = Queue::where([
                ['slot_time', '>=', $min_open_day],
                ['slot_time', '<=', $time_pick_up],
            ])
            ->orderBy('id', 'desc')
            ->get();
        } else {
            $queue = Queue::where([
                ['slot_time', '>=', $today_time],
                ['slot_time', '<=', $time_pick_up],
            ])
            ->orderBy('id', 'desc')
            ->get();
        }

        return $queue;
    }

    private function getProductQueueData($arr_of_rows, $user_quantity, $all_order_id, $MAX_QUANTITY)
    {
        $remaining_quantity = (int)$user_quantity;
        $pq_data = array();
        # check every queue available that less than pickup time
        foreach($arr_of_rows as $row)
        {
            if($remaining_quantity <= 0) { break; } // If remaining quantity after calc is no more

            $quantity_inside_table = 0; // Reset value

            #get all order available based on queue id
            $product_queue = DB::table('product_queue')
            ->whereIn('product_order_id', $all_order_id)
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
                    ];
                    $remaining_quantity -= $slot_availability; // Example = 15 - 14 = 1
                }
                else // NEXT ITERATION
                {
                    $pq_data[] = [
                        'quantity_slot' => $remaining_quantity, // Example = 1 -> INSERT
                        'queue_id' => $row->id,
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

    private function calculateTotalPrice($order_id)
    {
        $item_in_cart = ProductOrder::join('product_item', 'product_item.id', '=', 'product_order.product_item_id')
        ->where([
            ['pickup_order_id', $order_id],
            ['product_order.status', 1]
        ])
        ->select('product_order.quantity as quantity', 'product_item.price as price')
        ->get();

        $newTotalPrice = 0;
        
        foreach($item_in_cart as $row)
        {
            $newTotalPrice += doubleval($row->price * $row->quantity);
        }

        return $newTotalPrice;
    }

    public function showMerchantCart($id)
    {
        $cart_item = array(); // empty if cart is empty
        $user_id = Auth::id();

        $cart = PickUpOrder::where([
            ['status', 1],
            ['organization_id', $id],
            ['user_id', $user_id],
        ])->first();
        
        if($cart)
        {
            $cart_item = DB::table('product_order as po')
                    ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                    ->where('po.status', 1)
                    ->where('po.pickup_order_id', $cart->id)
                    ->select('po.id', 'po.quantity', 'pi.name', 'pi.price')
                    ->get();

            $pickup_date = Carbon::parse($cart->pickup_date)->format('d-m-Y H:i A');

            return view('merchant.cart', compact('cart', 'cart_item','id', 'pickup_date'));
        }
        else
        {
            // Coming soon - display page cart is empty
            return view('merchant.cart', compact('cart', 'cart_item' , 'id'));
        }
    }

    public function destroyCartItem(Request $request)
    {
        $id = $request->get('oc_id');

        $cart_item = ProductOrder::where('id', $id);

        $item = $cart_item->first();

        $cart_item->forceDelete();

        $allCartItem = DB::table('product_order')
                        ->where('pickup_order_id', $item->pickup_order_id)
                        ->where('status', 1)
                        ->count();
        
        // If cart is not empty
        if($allCartItem != 0)
        {
            $newTotalPrice = $this->calculateTotalPrice($item->pickup_order_id);
        }
        else
        {
            $newTotalPrice = null;
        }
        
        PickUpOrder::find($item->pickup_order_id)->update(['total_price' => $newTotalPrice]);
    }

    public function storeOrderAfterPayment(Request $request, $o_id, $p_id)
    {   
        PickUpOrder::find($p_id)->update([
            'note' => $request->note,
            'status' => 2,
        ]);

        ProductOrder::where('pickup_order_id', $p_id)->update(['status' => 2]);

        return redirect('/merchant')->with('success', 'Pesanan Berjaya direkodkan');
    }

    public function showOrder()
    {
        $userID = Auth::id();
        $total_price[] = 0;
        $pickup_date[] = 0;
        
        $order = DB::table('pickup_order as pu')
                ->join('organizations as o', 'pu.organization_id', '=', 'o.id')
                ->whereIn('status', [2,4])
                ->where('o.type_org', 2132)
                ->where('pu.user_id', $userID)
                ->select('pu.*', 'o.nama as merchant_name', 'o.telno as merchant_telno')
                ->orderBy('pu.status', 'desc')
                ->orderBy('pu.pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->paginate(5);

        foreach($order as $row)
        {
            $total_price[$row->id] = number_format($row->total_price, 2, '.', '');
            $pickup_date[$row->id] = Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
        }

        return view('merchant.order', compact('order', 'total_price', 'pickup_date'));
    }

    public function destroyOrder($id)
    {
        $order = PickUpOrder::find($id);
        $order->update(['status' => 200]);
        $result_order = $order->delete();
        
        $cart = ProductOrder::where('pickup_order_id', $id);
        $cart->update(['status' => 200]);
        $result_cart = $cart->delete();
        
        if($result_order && $result_cart)
        {
            Session::flash('success', 'Pesanan Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        }
        else
        {
            Session::flash('error', 'Pesanan Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    public function showHistory()
    {
        $userID = Auth::id();
        $total_price[] = 0;
        $pickup_date[] = 0;
        
        $history = DB::table('pickup_order as pu')
                ->join('organizations as o', 'pu.organization_id', '=', 'o.id')
                ->whereIn('status', [3,100,200])
                ->where('pu.user_id', $userID)
                ->where('o.type_org', 2132)
                ->select('pu.*', 'o.nama as merchant_name', 'o.telno as merchant_telno')
                ->orderBy('pu.status', 'desc')
                ->orderBy('pu.pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->paginate(5);

        foreach($history as $row)
        {
            $total_price[$row->id] = number_format($row->total_price, 2, '.', '');
            $pickup_date[$row->id] = Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
        }

        return view('merchant.history', compact('history', 'total_price', 'pickup_date'));
    }

    public function showList($id)
    {
        $userID = Auth::id();

        // Get Information about the order
        $list = DB::table('pickup_order as pu')
                ->join('organizations as o', 'pu.organization_id', '=', 'o.id')
                ->where('pu.id', $id)
                ->where('pu.status', '>' , 0)
                ->where('pu.user_id', $userID)
                ->select('pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                        'o.id','o.nama', 'o.parent_org', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state')
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
        
        foreach($item as $row)
        {
            $price[$row->id] = number_format($row->price, 2, '.', '');
            $total_price[$row->id] = number_format(doubleval($row->price * $row->quantity), 2, '.', ''); // calculate total for each item in cart
        }

        return view('merchant.list', compact('list', 'order_date', 'pickup_date', 'total_order_price', 'item', 'price', 'total_price'));
    }

    # Testing function for insert product group data and queue data
    public function testType(Request $request)
    {
        # Insert Product Group Data
        $pg = ProductGroup::create([
            'name' => $request->type_name,
            'duration' => $request->duration,
            'organization_id' => 4,
        ]);

        # Get Maximum and Minimum value of when the organization close(max) and open(min)
        $max = OrganizationHours::where('organization_id', 4)->max('close_hour');
        $min = OrganizationHours::where('organization_id', 4)->min('open_hour');

        # Formatting Value
        $max_f = Carbon::parse($max);
        $min_f = Carbon::parse($min);

        # Get Difference of Min and Max
        $diffTime = $max_f->diff($min_f)->format('%H:%I:%S');

        # Convert Time Difference into Minutes
        $time = Carbon::parse($diffTime); // Formatting
        $start_of_day = Carbon::parse($diffTime)->startOfDay(); // Get 00:00:00
        $total_minutes = $time->diffInMinutes($start_of_day); // Get minutes by differenciate time in minutes

        # Initialize variable
        $duration = $pg->duration;
        $temp = $min_f;
        $data = array(0 => ["slot_time" => $min, "status" => 1, "slot_number" => 1, "product_group_id" => $pg->id, "created_at" => Carbon::now(), "updated_at" => Carbon::now()]); // Initialize first row of queue (opening hour/ Min)

        # Loop until the total duration more or equal to total minutes
        for($newDuration = $duration, $i = 2; $newDuration <= $total_minutes; $newDuration += $duration, $i++)
        {
            $temp_f = $temp->addMinutes($duration)->format('H:i:s');  // Add minutes to current time var
            $data[] = [
                "slot_time" => $temp_f,
                "status" => 1,
                "slot_number" => $i,
                "product_group_id" => $pg->id,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ];
            $temp = Carbon::parse($temp_f); // Reformat time so it can perform addMinutes method
        }

        # Insert in Queue Table
        Queue::insert($data);

        return back();
    }
}
