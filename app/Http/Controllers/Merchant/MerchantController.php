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
        ->paginate(5);
        
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
        $pickup_date = "";

        $order = PickUpOrder::where([
            ['user_id', $user_id], ['organization_id', $org_id], ['status', 1]
        ])
        ->select('id', 'pickup_date')
        ->first();

         // Get all open day organization
        
        # If Order already exists for this organization
        if(!$order)
        {
            $day_body = $this->getDayBody($org_id);
        }
        else
        {
            $order_id = $order->id;
            $pickup_date = Carbon::parse($order->pickup_date)->format('Y-m-d h:i A');
            $isDelete = $this->deleteExpiredOrder($order_id);

            if($isDelete)
            {
                $day_body = $this->getDayBody($org_id);
            }
        }
        return response()->json(['order_id' => $order_id, 'pickup_date' => $pickup_date, 'path' => '/merchant/'.$org_id, 'day_body' => $day_body]);
    }

    private function getDayBody($organization_id)
    {
        $day_name_MY = array("Ahad", "Isnin", "Selasa", "Rabu", "Khamis", "Jumaat", "Sabtu");

        $body = "";
        $max_day = 7; // Check database organization

        $today_day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName(Carbon::now()->format('l')); // Return integer based on name

        $all_open_days = OrganizationHours::where('organization_id', $organization_id)->where('status', 1)->get();

        foreach($all_open_days as $row)
        {
            if($today_day == $row->day)
            {
                $body .= "<option value='".$row->day."'>Hari ini</option>";
                $day = $row->day;
            }
        }

        $all_day_except_open = OrganizationHours::where([
            ['organization_id', $organization_id],
            ['status', 1],
            ['day', '!=' , $day],
        ])->get();

        for($i = 0; $i < $max_day; $i++)
        {
            # If the day is Saturday, then change it to Sunday if it still looping
            if($day == 7 && $i < $max_day)
            {
                $day = 0;
            }
            
            if($day != $today_day)
            {
                foreach($all_day_except_open as $row)
                {
                    if($day == $row->day)
                    {
                        $body .= "<option value='".$day."'>".$day_name_MY[$day]."</option>";
                    }
                }
            }
            $day++;
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
            $min_required_time = $Today->addHour(1)->toTimeString(); // Add 1 hour to current time

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

        $product_item = DB::table('product_item as pi')
        ->join('product_group as pg', 'pg.id', '=', 'pi.product_group_id')
        ->where([
            ['pg.organization_id', $id],
            ['pg.deleted_at', NULL],
            ['pi.deleted_at', NULL],
        ])
        ->select('pi.id', 'pi.name as item_name', 'pi.desc', 'pi.price', 'pi.image', 'pi.status', 'pi.product_group_id')
        ->orderBy('pi.product_group_id', 'asc')
        ->orderBy('item_name')  
        ->get();

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

        $modal = $this->createQuantityBody($i_id, $o_id, $maxQuantity);

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $maxQuantity]);
    }

    private function createQuantityBody($item_id, $organization_id, $max_quantity_by_slot)
    {
        $user_id = Auth::id();

        $order = DB::table('product_order as po')->join('pickup_order as pu', 'pu.id', '=', 'po.pickup_order_id')
        ->where([
            ['pu.user_id', $user_id],
            ['pu.organization_id', $organization_id],
            ['po.product_item_id', $item_id],
            ['pu.status', 1],
        ])
        ->select('quantity')
        ->first();

        $body = '<div class="row justify-content-center"><i>Kuantiti Maximum : '.$max_quantity_by_slot.'</i></div>';
        if(!$order) {
            $body .= '<input id="quantity_input" type="text" value="1" name="quantity_input">';
        } else {
            $body .= '<input id="quantity_input" type="text" value="'.$order->quantity.'" name="quantity_input">';
            $body .= '<div class="row justify-content-center"><i>Dalam Troli : '.$order->quantity.'</i></div>';
        }

        return $body;
    }

    private function getMaxQuantityBySlotTime($item_id, $org_id)
    {
        $user_id = Auth::id();

        $order = PickUpOrder::where([
            ['user_id', $user_id],
            ['organization_id', $org_id],
            ['status', 1],
        ])->first();

        $min_open_day = $this->getOpenHourByDate($order->pickup_date, $org_id);
        
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

    private function getOpenHourByDate($dateOrder, $organization_id)
    {
        $todayDate = Carbon::parse($dateOrder)->format('l');
        $day = app('App\Http\Controllers\CooperativeController')->getDayIntegerByDayName($todayDate);
        $min_open_day = OrganizationHours::where('organization_id', $organization_id)->where('day', $day)->first()->open_hour;

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
        
        # Get in cart data based on item id and order id
        $cart = ProductOrder::where([
            ['status', 1],
            ['product_item_id', $i_id],
            ['pickup_order_id', $order->id],
        ]);

        $cartExist = $cart->exists();
        
        # If product queue data exists
        $temp_pqueue = $this->checkProductQueueExists($cart);

        $userOrderDateTime = $order->pickup_date;

        # Get ID for all order of the same day
        $allItem = $this->checkDateForQueue($userOrderDateTime, $o_id, $i_id);

        $item = ProductItem::find($i_id);
        $product_group_id = $item->product_group_id;
        $MAX_ITEM_QUANTITY = $item->quantity;

        # Get queue available based on day and time
        $queue = $this->getQueueData($userOrderDateTime, $o_id, $product_group_id);
        
        if(count($queue) != 0)
        {
            # Get data for product_queue
            $pq_data = $this->getProductQueueData($queue, $quantity, $allItem, $MAX_ITEM_QUANTITY);

            if($pq_data == 0) {
                if(count($temp_pqueue) != 0) {
                    foreach($temp_pqueue as $row)
                    {
                        DB::table('product_queue')->insert($row);
                    }
                }
                $msg = 'Harap maaf, kuantiti yang dimasukkan melebihi kekosongan yang ada';
                return response()->json(['alert' => $msg]);
            }
            else
            {
                if($cartExist)
                {
                    $cart->update(['quantity' => $quantity,]);
                    $p_o_id = $cart->first()->id;
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

    private function checkProductQueueExists($cart)
    {
        $temp_pqueue = array();
        $cartExist = $cart->exists();

        if($cartExist)
        {
            $p_o_id = $cart->first()->id;
            $p_queue = DB::table('product_queue')->where('product_order_id', $p_o_id)->get();
            # If product_queue of user order is exists -> delete data to replace
            if(count($p_queue) != 0)
            {
                foreach($p_queue as $row)
                {
                    $temp_pqueue[] = [
                        'quantity_slot' => $row->quantity_slot,
                        'product_order_id' => $row->product_order_id,
                        'queue_id' => $row->queue_id,
                    ];
                    
                    DB::table('product_queue')->where('id', $row->id)->delete();
                }
            }
        }

        return $temp_pqueue;
    }

    public function checkDateForQueue($order_datetime, $organization_id, $item_id)
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

    private function getQueueData($order_datetime, $organization_id, $pg_id)
    {   
        $min_open_day = $this->getOpenHourByDate($order_datetime, $organization_id);

        $today_date = Carbon::now()->toDateString();
        $today_time = Carbon::now()->minute(0)->second(0)->addHour()->toTimeString();

        $date_pick_up = Carbon::parse($order_datetime)->toDateString();
        $time_pick_up = Carbon::parse($order_datetime)->toTimeString();

        if($date_pick_up != $today_date) {
            $queue = Queue::where([
                ['product_group_id', $pg_id],
                ['slot_time', '>=', $min_open_day],
                ['slot_time', '<=', $time_pick_up],
            ])
            ->orderBy('id', 'desc')
            ->get();
        } else {
            $queue = Queue::where([
                ['product_group_id', $pg_id],
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
            $pickup_date[$row->id] = Carbon::parse($row->pickup_date)->format('d/m/y h:i A');
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
}
