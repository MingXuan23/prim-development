<?php

namespace App\Http\Controllers\Cooperative\User;
use App\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\MerchantOrderReceipt;

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
        $role_id = DB::table('type_organizations')->where('nama','Koperasi')->first()->id;
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
        
        if(count($orgID)==0){
            $isBuyer = DB::table('users as u')
                ->join('model_has_roles as r', 'u.id', '=', 'r.model_id')
                ->select('u.*')
                ->where('u.id',$userID)
                ->where('r.role_id',15)
                ->first();
            if($isBuyer!=null){
                $orgID = DB::table('organizations as k')
                ->join('organizations as o','o.id','k.parent_org')
                ->select('o.id', 'o.nama')
                ->distinct()
                ->get();

                $orgID = array_map(function ($org) {
                    $org->isBuyer = true;
                    return $org;
                }, $orgID->toArray());

            }
        }
        //$koperasi = Organization::where('type_org', $role_id)->select('id', 'nama', 'parent_org')->get();

        return view('koperasi.index', compact('orgID'));
    }

    public function fetchKoop(Request $request)
    {
        $sID = $request->get('sID');
        $isBuyer=$request->get('isBuyer');
        if($isBuyer==true){
            $koop = DB::table('organizations as o')
            ->join('organization_url as url','url.organization_id','o.id')
            ->where('o.parent_org', $sID)
            ->where('url.status',1)
            ->select('o.*','url.url_name')
            ->get();
        }
        else{
            $koop = DB::table('organizations as o')
            ->where('parent_org', $sID)
            ->select('o.*')
            ->get();
        }
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
    public function storeInCart(Request $request)
    {
        $userID = Auth::id();

        $item = ProductItem::where('id', $request->i_id)->first();
        // Check if quantity request is less or equal to quantity available
        if($request->qty <= $item->quantity_available) // if true
        {
            //dd($request);
            $order = PgngOrder::where([
                ['user_id', $userID],
                ['status', 1],
                ['organization_id', $request->o_id]
            ])->first();
            
            // Check if order already exists
            if($order) // order exists
            {
                $cartExist = ProductOrder::where([
                    ['product_item_id', $request->i_id],
                    ['pgng_order_id', $order->id],
                ])->first();
                
                // $cartExist = DB::table('product_order as po')
                //             ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                //             ->where('pg.status',1)
                //             ->where('po.product_item_id',$request->id)
                //             ->where('pg.id',$order->id)
                //             ->first();
                // If same item exists in cart
                if($cartExist) // if exists (update)
                {
                    // if($request->qty > $cartExist->quantity) // request quant more than existing quant
                    // {
                    //     $newQuantity = intval($item->quantity_available - ($request->qty - $cartExist->quantity)); // decrement stock
                    // }
                    // else if($request->qty < $cartExist->quantity) // request quant less than existing quant
                    // {
                    //     $newQuantity = intval($item->quantity_available + ($cartExist->quantity- $request->qty)); // increment stock
                    // }
                    // else if($request->qty == $cartExist->quantity) // request quant equal existing quant
                    // {
                    //     $newQuantity = intval((int)$item->quantity_available + 0); // stock not change
                    // }

                    ProductOrder::where('id', $cartExist->id)->update([
                        'quantity' => $request->qty
                    ]);//change the quantity in cart
                }
                else // if not exists (insert)
                {
                    ProductOrder::create([
                        'quantity' => $request->qty,
                        'status' => 1,
                        'product_item_id' => $request->i_id,
                        'pgng_order_id' => $order->id
                    ]);//create new order

                    $newQuantity = intval((int)$item->quantity_available - (int)$request->qty);
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

                $charge=$this->calCharge( $newTotalPrice,$request->o_id);

                
                $newTotalPrice+=doubleval($charge);
                PgngOrder::where([
                    ['user_id', $userID],
                    ['status', 1],
                    ['organization_id', $request->o_id]
                ])
                ->update([
                    'total_price' => $newTotalPrice
                ]);
            }
            else // order did not exists
            {
               
                    
                $totalPrice = $item->price * (int)$request->qty;
                $charge=$this->calCharge( $totalPrice,$request->o_id);
                $totalPrice+=doubleval($charge);
                $newQuantity = intval((int)$item->quantity_available - (int)$request->qty);

                $newOrder = PgngOrder::create([
                    'method_status' => 1,
                    'total_price' => $totalPrice,
                    'status' => 1,
                    'user_id' => $userID,
                    'organization_id' => $request->o_id
                ]);

                ProductOrder::create([
                    'quantity' => $request->qty,
                    'status' => 1,
                    'product_item_id' => $request->i_id,
                    'pgng_order_id' => $newOrder->id
                ]);
            }

            // // check if quantity is 0 after add to cart
            // if($newQuantity != 0) // if not 0
            // {
            //     ProductItem::where('id', $request->i_id)->update(['quantity_available' => $newQuantity]);
            // }
            // else // if 0 (change item status)
            // {
            //     ProductItem::where('id', $request->i_id)
            //     ->update(['quantity_available' => $newQuantity, 'status' => 0]);
            // }
            
            return back()->with('success', 'Item Berjaya Dikemaskini!');
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
    public function show(int $id)// strange to use??
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

        $org = Organization::where('id', $koperasi->id)->select('nama')->first();
        
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
    
    // to check quantity before user check out to pay
    public function checkCart(Request $request){
       
            $id=$request->pgngOrderId;
            $cart_item = array(); // empty if cart is empty
            $user_id = Auth::id();

            $cart = PgngOrder::where([
                ['status', 1],
                ['id', $id],
                ['user_id', $user_id],
            ])->first();
            if($cart)
            {   
                $before_cart_item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                ->where('pg.status', 1)
                ->where('po.pgng_order_id', $cart->id)
                ->select('pg.id as pgngId','po.id as productOrderId','pi.id as itemId', 'po.quantity', 'pi.name', 'pi.price', 'pi.image','pi.quantity_available','pi.status')
                ->get();

                foreach($before_cart_item as $item){
                    if($item->quantity_available===0 || $item->status===0){
                        return response()->json(['insufficientQuantity' => 1]);
                    }else if($item->quantity >$item->quantity_available){
                        return response()->json(['insufficientQuantity' => 1]);
                    }
                }
                return response()->json(['insufficientQuantity' => 0]);
            }
            return response()->json(['insufficientQuantity' => 1]);
    }

    public function edit($id) // user see their cart here
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
            $before_cart_item = DB::table('product_order as po')
            ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
            ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
            ->where('pg.status', 1)
            ->where('po.pgng_order_id', $cart->id)
            ->select('pg.id as pgngId','po.id as productOrderId','pi.id as itemId', 'po.quantity', 'pi.name', 'pi.price', 'pi.image','pi.quantity_available','pi.status')
            ->get();

            $org_id=DB::table('product_item as pi')
            ->join('product_group as pg','pg.id','=','pi.product_group_id')
            ->where('pi.id',$before_cart_item->first()->itemId)
            ->select('pg.organization_id')
            ->first()
            ->organization_id;      
            
            $updateMessage="";
            foreach($before_cart_item as $item){
                if($item->quantity_available===0 || $item->status===0){
                    $this->destroyProductOrder($item->productOrderId,$item->pgngId);
                    $updateMessage=$updateMessage.$item->name." was removed because not enough stock\n";
                }else if($item->quantity >$item->quantity_available){
                    ProductOrder::where([
                        ['id', $item->productOrderId],
                    ])->update(['quantity' => $item->quantity_available]);

                    $userID=Auth::id();

                    $newTotalPrice = 0;
                    // Recalculate total
                    $allCartItem = DB::table('product_order as po')
                        ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                        ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                        ->where('pg.id', $item->pgngId)
                        ->where('pg.status', 1)
                        ->select('po.quantity', 'pi.price')
                        ->get();

                    foreach($allCartItem as $row)
                    {
                        $newTotalPrice += doubleval($row->price * $row->quantity);
                    }
                    
                    $charge=$this->calCharge( $newTotalPrice,$org_id);    
                    $newTotalPrice += doubleval($charge);

                    PgngOrder::where([
                        ['user_id', $userID],
                        ['status', 1],
                        ['organization_id', $org_id],
                    ])->update(['total_price' => $newTotalPrice]);
                    $updateMessage=$updateMessage.$item->name." was updated because not enough stock\n";
                }
            }
            // $tomorrowDate = Carbon::tomorrow()->format('Y-m-d');

            //after validation
            $cart_item = DB::table('product_order as po')
            ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
            ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
            ->where('pg.status', 1)
            ->where('po.pgng_order_id', $cart->id)
            ->select('pg.id as pgngId','po.id as productOrderId', 'po.quantity', 'pi.name', 'pi.price', 'pi.image','pi.quantity_available','pi.status')
            ->get();

            $newTotalPrice=0;
            foreach($cart_item as $row)
            {
                $newTotalPrice += doubleval($row->price * $row->quantity);
            }
            
            $charge=$this->calCharge( $newTotalPrice,$org_id); 

            $allDay = OrganizationHours::where([
                ['organization_id', $id],
                //['status', 1],
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

            $cart = PgngOrder::where([
                ['status', 1],
                ['organization_id', $id],
                ['user_id', $user_id],
            ])->first();
            
            return view('koperasi.cart', compact('cart', 'cart_item', 'allDay', 'isPast' ,'id','updateMessage','charge'));
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

    public function destroyItemCart($org_id, Request $request) //remove a row in a cart when the quantity is set to 0
    {
        $userID = Auth::id();
        $id=$request->cart_id; //pgng order id
        
        $productOrderId=$request->productOrderInCartId;
        // $cart_item = ProductOrder::where('pgng_order_id', $id);
        $cart_item = ProductOrder::where([['pgng_order_id', $id],['id',$productOrderId]])->first();
        

        
        // $item=$cart_item->first();
        
        // //dd($item);
        // // $product_item = ProductOrder::where('product_item_id', $item->product_item_id);
        
        // $product_item = $item->product_item;
        // //return response()->json(['item' => ]);
        // $product_item_quantity = $product_item->first();

        // $newQuantity = intval($product_item_quantity->quantity_available + $item->quantity); // increment quantity

        /* If previous product item is being unavailable because of added item in cart,
           after the item deleted, the quantity in product_item will increment back and
           the item will be available */
        // if($product_item_quantity->quantity_available == 0)
        // {
        //     $product_item->update([
        //         'quantity_available' => $newQuantity,
        //         'status' => 1,
        //     ]);
        // }
        // else
        // {
        //     $product_item->update([
        //         'quantity_available' => $newQuantity,
        //     ]);
        // }
        
        
        $this->destroyProductOrder($cart_item->id,$id);
        
        
        return response()->json(['deleteId' => $productOrderId]);
        //return back()->with('success', 'Item Berjaya Dibuang');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroyProductOrder($poId, $id){
        $userID = Auth::id();
        $cart_item=ProductOrder::where([['pgng_order_id', $id],['id',$poId]]);

        $org_id=DB::table('product_item as pi')
        ->join('product_group as pg','pg.id','=','pi.product_group_id')
        ->where('pi.id',$cart_item->first()->product_item_id)
        ->select('pg.organization_id')
        ->first()
        ->organization_id;
        $cart_item->forceDelete();
        
        $allCartItem = DB::table('product_order as po')
                        ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                        ->join('pgng_orders as pg','po.pgng_order_id','=','pg.id')
                        ->where('pg.id', $id)
                        ->where('pg.status', 1)
                        ->select('po.quantity', 'pi.price')
                        ->get();
        
        // If cart is not empty
        if(count($allCartItem) > 0)
        {

            $newTotalPrice = 0;
            
            // Recalculate total
            foreach($allCartItem as $row)
            {
                $newTotalPrice += doubleval($row->price * $row->quantity);
            }

            //dd($newTotalPrice);
            $charge=$this->calCharge( $newTotalPrice,$org_id);
            
            $newTotalPrice += doubleval($charge);

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
    }

    public function destroy($id)
    {
        
    }

    public function indexOrder()
    {
        $type_id = DB::table('type_organizations')->where('nama','Koperasi')->first()->id;
        $userID = Auth::id();

        $query = DB::table('pgng_orders as ko')
                ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                ->whereIn('status', [2,4])
                ->where('user_id', $userID)
                ->where('type_org', $type_id)
                ->where('ko.deleted_at', null)
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
            
            if($isPast[$key]== 1)
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

        $order = $query->get();

        return view('koperasi.order', compact('order'));
    }

    public function indexHistory()
    {
        $role_id = DB::table('type_organizations')->where('nama','Koperasi')->first()->id;
        $userID = Auth::id();

        $order = DB::table('pgng_orders as ko')
                ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                ->join('users as u','u.id','ko.confirm_by')
                ->whereIn('status', [3, 100, 200])
                ->where('user_id', $userID)
                ->where('o.type_org', $role_id)
                ->select('ko.*', 'o.nama as koop_name', 'o.telno as koop_telno','u.name as confirmPerson')
                ->orderBy('ko.status', 'desc')
                ->orderBy('ko.pickup_date', 'asc')
                ->orderBy('ko.updated_at', 'desc')
                ->get();
        //dd($order);

        //$order = $query->paginate(5);
        $koperasiList="";
        $koperasi="";
        return view('koperasi.history', compact('order','koperasiList','koperasi'));
    }

    public function indexList($id)
    {

        $userID = Auth::id();
        // Get Information about the order

        $list_detail = DB::table('pgng_orders as ko')
                        ->join('organizations as o', 'ko.organization_id', '=', 'o.id')
                        ->join('transactions as t','t.id','ko.transaction_id')
                        ->where('ko.id', $id)
                        ->where('ko.status', '>' , 0)
                        ->where('ko.user_id', $userID)
                        ->select('ko.updated_at', 'ko.pickup_date', 'ko.total_price', 'ko.note', 'ko.status',
                                'o.id','o.nama', 'o.parent_org', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state','t.transac_no as fpxId')
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

        $previousUrl = url()->previous();
        $previousUrl = str_replace('/', '-', $previousUrl);
        //dd($previousUrl);
        return view('koperasi.list', compact('list_detail', 'allOpenDays', 'sekolah_name', 'item', 'totalPrice','previousUrl'));
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

    public function destroyUserOrder($id)//not to used
    {
        $resultKO = PgngOrder::find($id)->update(['status', 'Cancel by user'],['deleted_at',now()]);
        
        $resultPO = ProductOrder::where('pgng_order_id', $id)->delete();

        // $this->indexOrder(); // Recall function to recheck status
        
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
        $role_id = DB::table('type_organizations')->where('nama','Koperasi')->first()->id;
        $sekolah = DB::table('organizations')
                   ->where('type_org',$role_id)
                   ->get();
        return view('koop.index',compact('sekolah'))->with('sekolah',$sekolah);
    }

    public function koopShop($koop)
    {
        if(is_int($koop)){
            $id=$koop;
        }
        else{
            $id = DB::table('organization_url')
                ->where('url_name',$koop)
                ->where('status',1)
                ->first()
                ->organization_id;
        }
        $role_id = DB::table('type_organizations')->where('nama','Koperasi')->first()->id;
        $Sekolah = DB::table('organizations')
        ->where('type_org',$role_id)
        ->where('id',$id)
        ->first();
        $userId=Auth::id();

        $products = DB::table('product_group as pg')
         ->join('product_item as p','pg.id','p.product_group_id')
         ->select('p.*','pg.id as groupId','pg.name as groupName')
         ->where('pg.organization_id',$id) 
         ->whereNull('p.deleted_at')
         ->whereNull('pg.deleted_at')
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

        $parent = DB::table('users')
                            ->where('id',$userId)
                            ->first();

        $childrenByParent=DB::table('organization_user as ou')
        ->join('organization_user_student as ous','ou.id','=','ous.organization_user_id')
        ->join('students as s','s.id','=','ous.student_id')
        ->join('class_student as cs','cs.student_id','=','s.id')
        ->join('class_organization as co','co.id','=','cs.organclass_id')
        ->join('classes as c','c.id','=','co.class_id')
        ->select('s.*', 'ou.organization_id','c.nama as className','c.id as classId')
        ->where('ou.organization_id', $koperasi->parent_org)
        ->where('ou.user_id',$parent->id)
        ->where('ou.role_id', 6)
        ->where('ou.status', 1)
        ->orderBy('c.nama')
        ->get();

        //dd($childrenByParent);
        $k_open_hour = date('h:i A', strtotime($koperasi->open_hour));
        
        $k_close_hour = date('h:i A', strtotime($koperasi->close_hour));
        //dd($products);
        return view('koop.koop')
        ->with('Sekolah',$Sekolah)
        ->with('products',$products)
        ->with('koperasi',$koperasi)
        ->with('k_open_hour', $k_open_hour)
        ->with('k_close_hour', $k_close_hour)
        ->with('childrenByParent',$childrenByParent)
        ->with('parent',$parent);
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

    public function productsListByGroup(Request $request)
    {
        $id=$request->kooperasiId;
        $oid=Organization::where('id', $id)->select('parent_org')->first();
        
        //filter by Tahun
        $selectedTarget=$request->selectedGroup;
        $classId=0;
        $year=0;
        if($selectedTarget[0]==="C"){
            $classId= substr($selectedTarget, 1);
            $class = DB::table('classes as c')
            ->where('c.id',$classId)
            ->first();
            
            if($class){
                $classId= substr($selectedTarget, 1);//remove char c which determine the class
                $year=$class->nama[0];
            }
        }

        if($classId!==0){
            $products = DB::table('product_group as pg')
            ->join('product_item as p','pg.id','p.product_group_id')
            ->select('p.*','pg.id as groupId','pg.name as groupName')
            ->where('pg.organization_id',$id) 
            ->where(function($query) use ($classId,$year){
                $query->whereJsonContains('p.target->data', $classId)
                      ->orWhereJsonContains('p.target->data', 'T'.$year);
            })
            ->whereNull('p.deleted_at')
            ->whereNull('pg.deleted_at')
           ->get();  
        }
        else if (strpos($selectedTarget, "Tahun") !== false){
            $Tahun = str_replace("Tahun", "", $request->selectedGroup);
            $Class = DB::table('classes as c')
            ->where('c.nama', 'like', $Tahun . '%')
            ->join('class_organization', 'class_organization.class_id', '=', 'c.id')
            ->where('c.status', 1)
            ->where('class_organization.organization_id', $oid->parent_org)
            ->distinct()
            ->pluck('c.id')
            ->toArray();
                      
            $products = DB::table('product_group as pg')
            ->join('product_item as p','pg.id','p.product_group_id')
            ->select('p.*','pg.id as groupId','pg.name as groupName')
            ->where('pg.organization_id',$id)
            ->where(function ($query) use ($Class,$Tahun) {
                $query->whereJsonContains('p.target->data', "T".$Tahun);
                foreach ($Class as $class) {
                    $query->orWhereJsonContains('p.target->data', (string)$class);
                }})     
            ->whereNull('p.deleted_at')
            ->whereNull('pg.deleted_at')
           ->get();  

        }
        //get all product for All tahun (no specification of Tahun)
        else if($request->selectedGroup=="GeneralItem")
        {
            $products = DB::table('product_group as pg')
            ->join('product_item as p','pg.id','p.product_group_id')
            ->select('p.*','pg.id as groupId','pg.name as groupName')
            ->where('pg.organization_id',$id)
            ->whereJsonContains('p.target->data', 'All')
            ->whereNull('p.deleted_at')
            ->whereNull('pg.deleted_at')
           ->get();  
        }
        else if($request->selectedGroup=="AllItem")
        {
            $products = DB::table('product_group as pg')
            ->join('product_item as p','pg.id','p.product_group_id')
            ->select('p.*','pg.id as groupId','pg.name as groupName')
            ->where('pg.organization_id',$id)
            ->whereNull('p.deleted_at')
            ->whereNull('pg.deleted_at')
            //->whereJsonContains('p.target->data', 'ALL')
           ->get();  
        }
        //by category
        else{
            $products = DB::table('product_group as pg')
            ->join('product_item as p','pg.id','p.product_group_id')
            ->select('p.*','pg.id as groupId','pg.name as groupName')
            ->where('pg.organization_id',$id) 
            ->where('p.product_group_id',$request->selectedGroup)
            ->whereNull('p.deleted_at')
            ->whereNull('pg.deleted_at')
           ->get();  
        }

        if ($request->searchKey != "") {
            $searchKey = strtolower($request->searchKey);
            $products = $products->filter(function ($product) use ($searchKey) {
                return (strpos(strtolower($product->groupName), $searchKey) !== false)
                    || (strpos(strtolower($product->name), $searchKey) !== false);
            });
        }
       
        //return response()->json(['status' => "success"]);
        return response()->json(['products' => $products]);
    }

    public function fetchItemToModel(Request $request)
    {
        $i_id = $request->get('i_id');
        $o_id = $request->get('o_id');
        $user_id = Auth::id();
        $modal = '';
        
        $item = ProductItem::where('id', $i_id)
        ->select('id', 'type', 'name', 'price', 'quantity_available as qty')
        ->first();

        $order = DB::table('product_order as po')->join('pgng_orders as pu', 'pu.id', 'po.pgng_order_id')
        ->where([
            ['pu.user_id', $user_id],
            ['pu.organization_id', $o_id],
            ['po.product_item_id', $i_id],
            ['pu.status', 'In cart'],
        ])
        ->select('quantity as qty')
        ->first();
        
        if($order) { // Order exists in cart
            $max_quantity = $item->qty + ($order->qty);
        } else {
            $max_quantity = $item->qty;
        }
        
        $modal .= '<div class="row justify-content-center"><i>Kuantiti Inventori : '.$item->qty.'</i></div>';

        
        if(!$order) {
            $modal .= '<input id="quantity_input" type="text" value="1" name="quantity_input">';
            $modal .= '<div id="quantity-danger">Kuantiti Melebihi Inventori</div>';
        } else {
            $modal .= '<input id="quantity_input" type="text" value="'.$order->qty.'" name="quantity_input">';
            $modal .= '<div id="quantity-danger">Kuantiti Melebihi Inventori</div>';
            $modal .= '<div class="row justify-content-center"><i>Dalam Troli : '.$order->qty .'</i></div>';
        }

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $max_quantity]);
    }
    
    public function calCharge($total,$oid){
        $findCharge = DB::table('organization_charges')
                        ->where('organization_id', $oid)
                        ->where('minimum_amount', '<=', $total)
                        ->orderByDesc('minimum_amount')
                        ->first();
        if($findCharge==null)
        {
            $charge = DB::table('organizations')
            ->find($oid)
            ->fixed_charges;
        }
        else{
            $charge=$findCharge->remaining_charges;
        }

        if($charge==null){
            $charge=0;
        }
        //dd($findCharge,$total);
        return $charge;

    }
    //no need to un comment unless code
    // public function testPay(){
    //     $order = PgngOrder::where('transaction_id', 25853)->first();
    //     $transaction = Transaction::where('id', '=', 25853)->first();           
    //     PgngOrder::where('transaction_id', 25853)->first()->update([
    //         'status' => 2
    //     ]);
    //     $organization = Organization::where('id','=',$order->organization_id)->first();
    //     $user = User::where('id','=',$order->user_id)->first();

    //     $relatedProductOrder =DB::table('product_order')
    //     ->where('pgng_order_id',$order->id)
    //     ->select('product_item_id as itemId','quantity')
    //     ->get();

    //     foreach($relatedProductOrder as $item){
    //         $relatedItem=DB::table('product_item')
    //         ->where('id',$item->itemId);
            
    //         $relatedItemQuantity=$relatedItem->first()->quantity_available;

    //         $newQuantity= intval($relatedItemQuantity - $item->quantity);
           
    //         if($newQuantity<=0){
    //             $relatedItem
    //             ->update([
    //                 'quantity_available'=>0,
    //                 'status'=>0
    //             ]);
    //         }
    //         else{
    //             $relatedItem
    //             ->update([
    //                 'quantity_available'=>$newQuantity
    //         ]);
    //         }
    //         //dd($relatedItem);
    //     }
        
    //     $item = DB::table('product_order as po')
    //     ->join('product_item as pi', 'po.product_item_id', 'pi.id')
    //     ->where('po.pgng_order_id', $order->id)
    //     ->select('pi.name', 'po.quantity', 'pi.price')
    //     ->get();

    //     Mail::to($user->email)->send(new MerchantOrderReceipt($order, $organization, $transaction, $user));
        
    //     return view('merchant.receipt', compact('order', 'item', 'organization', 'transaction', 'user'));
    // }
}
