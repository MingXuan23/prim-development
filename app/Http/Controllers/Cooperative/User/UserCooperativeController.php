<?php

namespace App\Http\Controllers\Cooperative\User;

use App\Http\Controllers\Controller;
use App\Models\PickUpOrder;
use App\Models\ProductItem;
use App\Models\ProductOrder;
use App\Models\OrganizationHours;
use App\Models\Organization;
use App\Models\PgngOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;

class UserCooperativeController extends Controller
{
    public function index()
    {
        $userID = Auth::id();

        $stdID = DB::table('organization_user as ou')
                    ->join('organization_user_student as ous', 'ou.id', '=', 'ous.organization_user_id')
                    ->where('ou.user_id', $userID)
                    ->select('ous.student_id')
                    ->get();

        $sID = array();
        foreach($stdID as $row)
        {
            $sID[] += $row->student_id;
        }

        $orgID = DB::table('class_student as cs')
                    ->join('class_organization as co', 'cs.organclass_id', '=', 'co.id')
                    ->join('organizations as o', 'co.organization_id', '=', 'o.id')
                    ->whereIn('cs.student_id', $sID)
                    ->select('o.id', 'o.nama')
                    ->distinct()
                    ->get();
        
        $koperasi = Organization::where('type_org', 1039)->select('id', 'nama', 'parent_org')->get();

        return view('koperasi.index', compact('koperasi', 'orgID'));
    }

    public function fetchKoop(Request $request)
    {
        $sID = $request->get('sID');
        
        $koop = Organization::where('parent_org', $sID)->select('id', 'nama')->get();

        return response()->json(['success' => $koop]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userID = Auth::id();

        $item = ProductItem::where('id', $request->item_id)->first();

        // Check if quantity request is less or equal to quantity available
        if($request->item_quantity <= $item->quantity_available) // if true
        {
            $order = PgngOrder::where([
                ['user_id', $userID],
                ['status', 1],
                ['organization_id', $request->org_id]
            ])->first();
            
            // Check if order already exists
            if($order) // order exists
            {
                // $cartExist = ProductOrder::where([
                //     ['product_item_id', $request->item_id],
                //     ['pgng_order_id', $order->id],
                // ])->first();
                $cartExist = DB::table('product_order as po')
                            ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                            ->where('pg.status',1)
                            ->where('po.product_item_id',$request->id)
                            ->where('pg.id',$order->id)
                            ->first();
                // If same item exists in cart
                if($cartExist) // if exists (update)
                {
                    if($request->item_quantity > $cartExist->quantity) // request quant more than existing quant
                    {
                        $newQuantity = intval($item->quantity_available - ($request->item_quantity - $cartExist->quantity)); // decrement stock
                    }
                    else if($request->item_quantity < $cartExist->quantity) // request quant less than existing quant
                    {
                        $newQuantity = intval($item->quantity_available + ($cartExist->quantity- $request->item_quantity)); // increment stock
                    }
                    else if($request->item_quantity == $cartExist->quantity) // request quant equal existing quant
                    {
                        $newQuantity = intval((int)$item->quantity_available - 0); // stock not change
                    }

                    ProductOrder::where('id', $cartExist->id)->update([
                        'quantity' => $request->item_quantity
                    ]);
                }
                else // if not exists (insert)
                {
                    ProductOrder::create([
                        'quantity' => $request->item_quantity,
                        'status' => 1,
                        'product_item_id' => $request->item_id,
                        'pgng_order_id' => $order->id
                    ]);

                    $newQuantity = intval((int)$item->quantity_available - (int)$request->item_quantity);
                }


                $cartItem = DB::table('product_order as po')
                                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                                ->where('po.pgng_order_id', $order->id)
                                ->select('po.quantity', 'pi.price')
                                ->get();
                
                $newTotalPrice = 0;
                
                foreach($cartItem as $row)
                {
                    $newTotalPrice += doubleval($row->price * $row->quantity);
                }

                PgngOrder::where([
                    ['user_id', $userID],
                    ['status', 1],
                    ['organization_id', $request->org_id]
                ])
                ->update([
                    'total_price' => $newTotalPrice
                ]);
            }
            else // order did not exists
            {
                $totalPrice = $item->price * (int)$request->item_quantity;

                $newQuantity = intval((int)$item->quantity_available - (int)$request->item_quantity);

                $newOrder = PgngOrder::create([
                    'method_status' => 1,
                    'total_price' => $totalPrice,
                    'status' => 1,
                    'user_id' => $userID,
                    'organization_id' => $request->org_id
                ]);

                ProductOrder::create([
                    'quantity' => $request->item_quantity,
                    'status' => 1,
                    'product_item_id' => $request->item_id,
                    'pgng_order_id' => $newOrder->id
                ]);
            }

            // check if quantity is 0 after add to cart
            if($newQuantity != 0) // if not 0
            {
                ProductItem::where('id', $request->item_id)->update(['quantity_available' => $newQuantity]);
            }
            else // if 0 (change item status)
            {
                ProductItem::where('id', $request->item_id)
                ->update(['quantity_available' => $newQuantity, 'status' => 0]);
            }
            
            return back()->with('success', 'Item Berjaya Ditambah!');
        }
        else // if false
        {
            $message = "Stock Barang Ini Tidak Mencukupi | Stock : ".$item->quantity_available;
            return back()->with('error', $message);
        }
        // dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todayDate = Carbon::now()->format('l');

        $day = $this->getDayIntegerByDayName($todayDate);

        $koperasi = DB::table('organizations as o')
                    ->join('organization_hours as oh', 'o.id', '=', 'oh.organization_id')
                    ->where('o.id', $id)
                    ->where('oh.day', $day)
                    ->select('o.id', 'o.nama', 'o.telno', 'o.address', 'o.city', 'o.postcode', 'o.state', 'o.parent_org',
                            'oh.day', 'oh.open_hour', 'oh.close_hour', 'oh.status')
                    ->first();

        $org = Organization::where('id', $koperasi->parent_org)->select('nama')->first();
        
        $product_item = DB::table('product_item as pi')
                        ->join('product_group as pt', 'pi.product_group_id', '=', 'pt.id')
                        ->where('pt.organization_id', $koperasi->id)
                        ->select('pi.*', 'pt.name as type_name')
                        ->orderBy('pi.name')
                        ->get();
        
        $product_type = DB::table('product_group as pt')
                            ->join('product_item as pi', 'pt.id', '=', 'pi.product_group_id')
                            ->select('pt.id as type_id', 'pt.name as type_name')
                            ->where('pt.organization_id', $koperasi->id)
                            ->distinct()
                            ->get();

        $k_open_hour = date('h:i A', strtotime($koperasi->open_hour));
        
        $k_close_hour = date('h:i A', strtotime($koperasi->close_hour));

        return view('koperasi.menu', compact('koperasi', 'org', 'product_item', 'product_type', 'k_open_hour', 'k_close_hour'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cart_item = array(); // empty if cart is empty
        $user_id = Auth::id();

        $cart = PgngOrder::where([
            ['status', 1],
            ['organization_id', $id],
            ['user_id', $user_id],
        ])->first();
        
        if($cart)
        {
            // $cart_item = DB::table('product_order as po')
            //         ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
            //         ->where('po.status', 1)
            //         ->where('po.pgng_order_id', $cart->id)
            //         ->select('po.id', 'po.quantity', 'pi.name', 'pi.price', 'pi.image')
            //         ->get();
            $cart_item = DB::table('product_order as po')
            ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
            ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
            ->where('pg.status', 1)
            ->where('po.pgng_order_id', $cart->id)
            ->select('pg.id', 'po.quantity', 'pi.name', 'pi.price', 'pi.image')
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
        $org = PgngOrder::where('id', $id)->select('organization_id as id')->first();

        $daySelect = (int)$request->week_status;
                
        $pickUp = Carbon::now()->next($daySelect)->toDateString();
        
        PgngOrder::where([
            ['status', 1],
            ['id', $id],
        ])->update([
            'pickup_date' => $pickUp,
            'note' => $request->note,
            'status' => 2,
        ]);

        // ProductOrder::where([
        //     ['status', 1],
        //     ['pickup_order_id', $id],
        // ])->update(['status' => 2]);

        return redirect('/koperasi/koop/'.$org->id)->with('success', 'Pesanan Anda Berjaya Direkod!');
    }

    public function destroyItemCart($org_id, $id)
    {
        $userID = Auth::id();

        // $cart_item = ProductOrder::where('pgng_order_id', $id);
        $cart_item = ProductOrder::where('pgng_order_id', $id);

        $item = $cart_item->first();

        // $product_item = ProductOrder::where('product_item_id', $item->product_item_id);
        $product_item = ProductItem::where('id', $item->product_item_id);

        $product_item_quantity = $product_item->first();

        $newQuantity = intval($product_item_quantity->quantity_available + $item->quantity); // increment quantity

        /* If previous product item is being unavailable because of added item in cart,
           after the item deleted, the quantity in product_item will increment back and
           the item will be available */
        if($product_item_quantity->quantity_available == 0)
        {
            $product_item->update([
                'quantity_available' => $newQuantity,
                'status' => 1,
            ]);
        }
        else
        {
            $product_item->update([
                'quantity_available' => $newQuantity,
            ]);
        }

        $cart_item->forceDelete();

        $allCartItem = DB::table('product_order as po')
                        ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                        ->join('pgng_Orders as pg','po.pgng_order_id','=','pg.id')
                        ->where('pg.id', $item->pgng_order_id)
                        ->where('pg.status', 1)
                        ->select('po.quantity', 'pi.price')
                        ->get();
        
        // If cart is not empty
        if(count($allCartItem) != 0)
        {

            $newTotalPrice = 0;
            
            // Recalculate total
            foreach($allCartItem as $row)
            {
                $newTotalPrice += doubleval($row->price * $row->quantity);
            }

            PgngOrder::where([
                ['user_id', $userID],
                ['status', 1],
                ['organization_id', $org_id],
            ])->update(['total_price' => $newTotalPrice]);
        }
        else // If cart is empty (delete order)
        {
            PgngOrder::where([
                ['user_id', $userID],
                ['status', 1],
                ['organization_id', $org_id],
            ])->forceDelete();
        }
        

        return back()->with('success', 'Item Berjaya Dibuang');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    public function indexOrder()
    {
        $userID = Auth::id();

        $query = DB::table('pgng_orders as ko')
                ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                ->whereIn('status', [2,4])
                ->where('user_id', $userID)
                ->where('type_org', 1039)
                ->select('ko.*', 'o.nama as koop_name', 'o.telno as koop_telno')
                ->orderBy('ko.status', 'desc')
                ->orderBy('ko.pickup_date', 'asc')
                ->orderBy('ko.updated_at', 'desc');

        $all_order = $query->get();

        $allPickUpDate = array();
        $isPast = array();

        foreach($all_order as $row)
        {
            $allPickUpDate[] += date(strtotime($row->pickup_date));

            $key = date("Y-m-d", date(strtotime($row->pickup_date)));

            $pickUpDate = Carbon::parse($row->pickup_date)->startofDay();
            $todayDate = Carbon::now()->startOfDay();

            // Check if today is the day of the pickup day or is still not yet arrived
            if($todayDate->lte($pickUpDate))
            {
                $isPast[$key] = 0;
            }
            else
            {
                $isPast[$key] = 1;
            }

            if($row->pickup_date == $key && $isPast[$row->pickup_date] == 1)
            {
                // Status changed to overdue
                PgngOrder::where('pickup_date', $row->pickup_date)->update(['status' => 4]);
            }
            else
            {
                // Status is still not picked
                PgngOrder::where('pickup_date', $row->pickup_date)->update(['status' => 2]);
            }
        }

        $order = $query->paginate(5);

        return view('koperasi.order', compact('order'));
    }

    public function indexHistory()
    {
        $userID = Auth::id();

        $query = DB::table('pgng_orders as ko')
                ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                ->whereIn('status', [3, 100, 200])
                ->where('user_id', $userID)
                ->where('o.type_org', 1039)
                ->select('ko.*', 'o.nama as koop_name', 'o.telno as koop_telno')
                ->orderBy('ko.status', 'desc')
                ->orderBy('ko.pickup_date', 'asc')
                ->orderBy('ko.updated_at', 'desc');

        $order = $query->paginate(5);

        return view('koperasi.history', compact('order'));
    }

    public function indexList($id)
    {
        $userID = Auth::id();

        // Get Information about the order
        $list_detail = DB::table('pgng_orders as ko')
                        ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                        ->where('ko.id', $id)
                        ->where('ko.status', '>' , 0)
                        ->where('ko.user_id', $userID)
                        ->select('ko.updated_at', 'ko.pickup_date', 'ko.total_price', 'ko.note', 'ko.status',
                                'o.id','o.nama', 'o.parent_org', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state')
                        ->first();

        $date = Carbon::createFromDate($list_detail->pickup_date); // create date based on pickup date

        $day = $this->getDayIntegerByDayName($date->format('l')); // get day in integer based on day name

        // get open and close hour org
        $allOpenDays = OrganizationHours::where([
            ['organization_id', $list_detail->id],
            ['day', $day],
        ])->first();

        // get parent name
        $parent_org = Organization::where('id', $list_detail->parent_org)->select('nama')->first();

        $sekolah_name = $parent_org->nama;

        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                ->where('po.pgng_order_id', $id)
                ->where('pg.status', '>', 0)
                ->select('pi.name', 'pi.price', 'po.quantity')
                ->get();
                // dd($item);

        $totalPrice = array();
        
        foreach($item as $row)
        {
            $key = strval($row->name); // key based on item name
            $totalPrice[$key] = doubleval($row->price * $row->quantity); // calculate total for each item in cart
        }

        return view('koperasi.list', compact('list_detail', 'allOpenDays', 'sekolah_name', 'item', 'totalPrice'));
    }

    public function fetchAvailableDay(Request $request)
    {   
        $order_id = $request->get('oID');

        $order = PgngOrder::where('id', $order_id)->first();

        $allDay = OrganizationHours::where('organization_id', $order->organization_id)->where('status', 1)->get();
        
        $isPast = array();
            
        foreach($allDay as $row)
        {
            $TodayDate = Carbon::now()->format('l');

            $day = $this->getDayIntegerByDayName($TodayDate);

            $key = strval($row->day);

            $isPast = $this->getDayStatus($day, $row->day, $isPast, $key);

        }
        return response()->json(['day' => $allDay, 'past' => $isPast ]);
    }

    public function updatePickUpDate(Request $request)
    {
        $order_id = $request->get('oID');
        $daySelect = (int)$request->get('day');

        $pickUp = Carbon::now()->next($daySelect)->toDateString();

        $result = PgngOrder::where('id', $order_id)->update(['pickup_date' => $pickUp]);
        
        $this->indexOrder(); // Recall function to recheck status

        if ($result) {
            Session::flash('success', 'Hari Pengambilan Berjaya diubah');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Hari Pengambilan Tidak Berjaya diubah');
            return View::make('layouts/flash-messages');
        }
    }

    public function destroyUserOrder($id)
    {
        $queryKO = PgngOrder::where('id', $id);
        $queryKO->update(['status' => 200]);
        $resultKO = $queryKO->delete();
        
        $queryPO = ProductOrder::where('pgng_order_id', $id);

        $order = $queryPO->get();

        // Increment product_item quantity after deleted
        foreach($order as $row)
        { 
            ProductItem::where('id', $row->product_item_id)->increment('quantity', $row->quantity);
        }

        $queryPO->update(['status' => 200]);
        $resultPO = $queryPO->delete();

        $this->indexOrder(); // Recall function to recheck status
        
        if ($resultKO && $resultPO) {
            Session::flash('success', 'Pesanan Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    /*-------------------------- START KOOP SHOP --------------------------*/

    public function indexKoop()
    {
        $sekolah = DB::table('organizations')
                   ->where('type_org',1039)
                   ->get();
        return view('koop.index',compact('sekolah'))->with('sekolah',$sekolah);
    }

    public function koopShop(Int $id)
    {
        $sekolah = DB::table('organizations')
        ->where('type_org',1039)
        ->where('id',$id)
        ->get();

        $product = DB::table('product_group as p')
         ->join('product_item as pg','p.id','pg.product_group_id')
         ->where('p.organization_id',$id)
        ->get();

        
        $todayDate = Carbon::now()->format('l');

        $day = $this->getDayIntegerByDayName($todayDate);

        $koperasi = DB::table('organizations as o')
        ->join('organization_hours as oh', 'o.id', '=', 'oh.organization_id')
        ->where('o.id', $id)
        ->where('oh.day', $day)
        ->select('o.id', 'o.nama', 'o.telno', 'o.address', 'o.city', 'o.postcode', 'o.state', 'o.parent_org',
                'oh.day', 'oh.open_hour', 'oh.close_hour', 'oh.status')
        ->first();

        $k_open_hour = date('h:i A', strtotime($koperasi->open_hour));
        
        $k_close_hour = date('h:i A', strtotime($koperasi->close_hour));

        return view('koop.koop')
        ->with('sekolah',$sekolah)
        ->with('product',$product)
        ->with('koperasi',$koperasi)
        ->with('k_open_hour', $k_open_hour)
        ->with('k_close_hour', $k_close_hour);
    }

    public function storeKoop()
    {
        return redirect('koperasi/koop');
    }

    public function koopCart(Int $id)
    {
        $sekolah = DB::table('organizations')
        ->where('type_org',1039)
        ->where('id',$id)
        ->get();

        return view('koop.cart',)->with('sekolah',$sekolah);
    }

    /*-------------------------- END KOOP SHOP --------------------------*/

    public function getDayStatus($todayDay, $allDay, $arr, $key_index)
    {
        // If today is Sunday
        if($todayDay == 0) 
        { $arr[$key_index] = "Minggu Hadapan"; } // Pick up date always next week
        else
        {
            // if array of day available is sunday
            if($allDay == 0) { $arr[$key_index] = "Minggu Ini"; } // Pick up date for Sunday always this week
            // if today day is passed or today
            else if($todayDay >= $allDay) { $arr[$key_index] = "Minggu Hadapan"; } // Pick up date must next week
            // if today day is not passed yet
            else if($todayDay < $allDay) { $arr[$key_index] = "Minggu Ini"; } // Pick up date available this week
        }

        return $arr;
    }

    public function getDayIntegerByDayName($date)
    {
        $day = null;
        if($date == "Monday") { $day = 1; }
        else if($date == "Tuesday") { $day = 2; }
        else if($date == "Wednesday") { $day = 3; }
        else if($date == "Thursday") { $day = 4; }
        else if($date == "Friday") { $day = 5; }
        else if($date == "Saturday") { $day = 6; }
        else if($date == "Sunday") { $day = 0; }
        return $day;
    }
}
