<?php

namespace App\Http\Controllers;

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

class MerchantController extends Controller
{
    public function merchantList()
    {
        $todayDate = Carbon::now()->format('l');

        $day = app(CooperativeController::class)->getDayIntegerByDayName($todayDate);

        $merchant = Organization::with(['organization_hours' => function($q) use ($day){
            $q->where('organization_hours.day', $day);
        }])
        ->where('type_org', 2132)
        ->get();
        
        foreach($merchant as $row)
        {
            $oh_status[$row->id] = $row->organization_hours->first()->status;
        }

        // dd($oh_status);

        return view('merchant.index', compact('merchant', 'oh_status'));
    }

    public function fetchDay(Request $request)
    {
        $Today = Carbon::now();
        $dayNameMY = array("Ahad", "Isnin", "Selasa", "Rabu", "Khamis", "Jumaat", "Sabtu");
        $dayNameEN = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

        $user_id = Auth::id();
        $org_id = $request->get('o_id');
        $day_body = "";

        $exist = PickUpOrder::where([['user_id', $user_id], ['organization_id', $org_id], ['status', 1]])->exists();
        
        if(!$exist)
        {
            $allDay = OrganizationHours::where('organization_id', $org_id)->where('status', 1)->get();
        
            $isPast = array();
            
            foreach($allDay as $row)
            {
                $TodayDay = $Today->format('l'); // Get Day Name

                $day = app(CooperativeController::class)->getDayIntegerByDayName($TodayDay); // Return integer based on name

                $key = strval($row->day);

                $isPast = app(CooperativeController::class)->getDayStatus($day, $row->day, $isPast, $key); // Check and return day is past or this week
            }

            foreach($allDay as $row)
            {
                if($TodayDay == $dayNameEN[$row->day]) // If day array is Today
                {
                    $day_body .= "<option value='".$row->day."'>Hari ini</option>";
                }
                else
                {
                    $day_body .= "<option value='".$row->day."'>". $dayNameMY[$row->day]." ".$isPast[$row->day]."</option>";
                }
            }

            return response()->json(['day_body' => $day_body]);
        }
        else
        {
            return response()->json(['path' => '/merchant/'.$org_id, 'day_body' => $day_body]);
        }
    }

    public function fetchTime(Request $request)
    {
        $org_id = $request->get('o_id');
        $daySelected = $request->get('day');

        $Today = Carbon::now();
        
        $TodayDay = $Today->format('l');

        $TodayDayInt = app(CooperativeController::class)->getDayIntegerByDayName($TodayDay);

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

        $dayInt = app(CooperativeController::class)->getDayIntegerByDayName($todayDate);

        $date = Carbon::now()->toDateString();
        
        if($daySelect != $dayInt)
        {
            $date = Carbon::now()->next($daySelect)->toDateString();
        }

        $pickUp = $date.' '.$timeSelect.':00';

        return $pickUp;
    }

    public function showMerchant($id)
    {
        $todayDate = Carbon::now()->format('l');

        $day = app(CooperativeController::class)->getDayIntegerByDayName($todayDate);

        $merchant = Organization::with(['organization_hours' => function($q) use ($day){
            $q->where('organization_hours.day', $day);
        }])
        ->where('id', $id)
        ->first();

        $oh = $merchant->organization_hours->first();

        $open_hour = date('h:i A', strtotime($oh->open_hour));
        
        $close_hour = date('h:i A', strtotime($oh->close_hour));

        $product_item = ProductItem::with(['product_group' => function($q) use ($id){
            $q->where('product_group.organization_id', $id);
        }])
        ->orderBy('name')
        ->get();

        $product_type = ProductGroup::where('organization_id', $id)
        ->get();

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
                        'type_status' => $type->status,
                    ];
                    
                    $product_price[$item->id] = number_format((double)$item->price, 2, '.', '') ;
                }
            }
        }
        $jenis = array_unique($temp, SORT_REGULAR);

        return view('merchant.show', compact('merchant', 'oh', 'product_item', 'open_hour', 'close_hour', 'jenis', 'product_price'));
    }

    public function fetchItem(Request $request)
    {
        $id = $request->get('i_id');
   
        $item = ProductItem::where('id', $id)
        ->select('id', 'name', 'price', 'quantity', 'status')
        ->first();

        $modal = '';

        $modal = '<div class="text-center">Quantity Available : '.$item->quantity.'</div>';
        $modal = $modal.'<div class="d-inline"><input id="quantity_input" type="text" value="1" name="quantity_input"></div>';

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $item->quantity]);
    }

    public function storeItem(Request $request)
    {
        $i_id = $request->get('i_id');
        $o_id = $request->get('o_id');
        $quantity = $request->get('quantity');

        $userID = Auth::id();

        $item = ProductItem::where('id', $i_id)->first();

        // Check if quantity request is less or equal to quantity available
        if($quantity <= $item->quantity) // if true
        {
            $order = PickUpOrder::where([
                ['user_id', $userID],
                ['status', 1],
                ['organization_id', $o_id]
            ])->first();
            
            // Check if order already exists
            if($order) // order exists
            {
                $cartExist = ProductOrder::where([
                    ['status', 1],
                    ['product_item_id', $i_id],
                    ['pickup_order_id', $order->id],
                ])->first();

                // If same item exists in cart
                if($cartExist) // if exists (update)
                {
                    if($quantity > $cartExist->quantity) // request quant more than existing quant
                    {
                        $newQuantity = intval($item->quantity - ($quantity - $cartExist->quantity)); // decrement stock
                    }
                    else if($quantity < $cartExist->quantity) // request quant less than existing quant
                    {
                        $newQuantity = intval($item->quantity + ($cartExist->quantity - $quantity)); // increment stock
                    }
                    else if($quantity == $cartExist->quantity) // request quant equal existing quant
                    {
                        $newQuantity = intval((int)$item->quantity - 0); // stock not change
                    }

                    ProductOrder::where('id', $cartExist->id)->update([
                        'quantity' => $quantity
                    ]);
                }
                else // if not exists (insert)
                {
                    ProductOrder::create([
                        'quantity' => $quantity,
                        'status' => 1,
                        'product_item_id' => $i_id,
                        'pickup_order_id' => $order->id
                    ]);

                    $newQuantity = intval((int)$item->quantity - (int)$quantity);
                }

                $cartItem = DB::table('product_order as po')
                                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                                ->where('po.pickup_order_id', $order->id)
                                ->where('po.status', 1)
                                ->select('po.quantity', 'pi.price')
                                ->get();

                $newTotalPrice = 0;
                
                foreach($cartItem as $row)
                {
                    $newTotalPrice += doubleval($row->price * $row->quantity);
                }

                PickUpOrder::where([
                    ['user_id', $userID],
                    ['status', 1],
                    ['organization_id', $o_id]
                ])
                ->update([
                    'total_price' => $newTotalPrice
                ]);
            }
            else // order did not exists
            {
                $totalPrice = $item->price * (int)$quantity;

                $newQuantity = intval((int)$item->quantity - (int)$quantity);

                $newOrder = PickUpOrder::create([
                    'total_price' => $totalPrice,
                    'status' => 1,
                    'user_id' => $userID,
                    'organization_id' => $o_id
                ]);

                ProductOrder::create([
                    'quantity' => $quantity,
                    'status' => 1,
                    'product_item_id' => $i_id,
                    'pickup_order_id' => $newOrder->id
                ]);
            }

            // check if quantity is 0 after add to cart
            if($newQuantity != 0) // if not 0
            {
                ProductItem::where('id', $i_id)->update(['quantity' => $newQuantity]);
            }
            else // if 0 (change item status)
            {
                ProductItem::where('id', $i_id)
                ->update(['quantity' => $newQuantity, 'status' => 0]);
            }
            Session::flash('success', 'Item Berjaya Ditambah');
            return View::make('layouts/flash-messages');
        }
        else // if false
        {
            $message = "Stock Barang Ini Tidak Mencukupi | Stock : ".$item->quantity;
            Session::flash('error', $message);
            return View::make('layouts/flash-messages');
        }
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
                    ->select('po.id', 'po.quantity', 'pi.name', 'pi.price', 'pi.image')
                    ->get();

            // $tomorrowDate = Carbon::tomorrow()->format('Y-m-d');

            $allDay = OrganizationHours::where([
                ['organization_id', $id],
                ['status', 1],
            ])->get();
            
            $isPast = array();
            
            foreach($allDay as $row)
            {
                $TodayDate = Carbon::now()->format('l');
                // $MondayNextWeek = Carbon::now()->next(1);

                $day = $this->getDayIntegerByDayName($TodayDate);

                $key = strval($row->day);
                
                $isPast = $this->getDayStatus($day, $row->day, $isPast, $key);
            }
            return view('koperasi.cart', compact('cart', 'cart_item', 'allDay', 'isPast' ,'id'));
        }
        else
        {
            return view('koperasi.cart', compact('cart', 'cart_item' , 'id'));
        }
        return view('merchant.cart');
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
        $data = array(0 => ["slot_time" => $min, "status" => 1, "product_group_id" => $pg->id, "created_at" => Carbon::now(),
        "updated_at" => Carbon::now()]); // Initialize first row of queue (opening hour/ Min)

        # Loop until the total duration more or equal to total minutes
        for($newDuration = $duration; $newDuration <= $total_minutes; $newDuration += $duration)
        {
            $temp_f = $temp->addMinutes($duration)->format('H:i:s');  // Add minutes to current time var
            $data[] = [
                "slot_time" => $temp_f,
                "status" => 1,
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

    # Testing function insert Item for Slot
    public function testItem(Request $request)
    {
        # Get What Group ID the User Chose
        $group_id = 8; // <- Value for Testing only

        # Store Item Data
        $item = ProductItem::create([
            'name' => $request->item_name,
            'quantity' => $request->item_quantity,
            'price' => $request->item_price,
            'status' => 1,
            'product_group_id' => $group_id
        ]);

        # Get All Slot Time by Group ID
        $queue = Queue::where('product_group_id', $group_id)->get();

        # Store All Queue ID and Requested Item ID in an array
        foreach($queue as $row)
        {
            $queue_id[] = [
                "product_item_id" => $item->id,
                "queue_id" => $row->id,
            ];
        }

        # insert bridge data
        DB::table('product_queue')->insert($queue_id);

        return back();
    }   
}
