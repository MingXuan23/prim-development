<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Http\Jajahan\Jajahan;
use App\Models\OrganizationHours;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\TypeOrganization;
use App\Models\OrganizationRole;
use App\Models\Dish_Available;
use App\Models\Order;
use App\Models\Order_Dish;
use App\Models\Dish;
use App\Models\Dish_Type;
use View;
use DateTime;
use DateInterval;
use DatePeriod;
use Hash;
use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;


class OrderSController extends Controller
{   
    public function managemenu(){
        $orgtype = 'OrderS';
        $userId = Auth::id();
        $data = DB::table('organizations as o')
        ->leftJoin('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->leftJoin('type_organizations as to', 'o.type_org', '=', 'to.id')
        ->select("o.*")
        ->distinct()
        ->where('to.nama', $orgtype)
        ->where('ou.user_id',$userId)
        ->get();

        return view('orders.menu', compact('data'));
    }

    public function listmenu($organizationId){
        //dd($organizationId);
        $userId = Auth::id();
        $data = Dish::join('dish_type as dt', 'dt.id', '=', 'dishes.dish_type')
        ->where('organ_id',$organizationId)
        ->select('dishes.id', 'dishes.name as dishname', 'dishes.price', 'dt.name as dishtype')
        ->get();

        $org_name = Organization::where('id',$organizationId)
        ->select('nama')
        ->get();
        //dd($org_name);

        // foreach ($org_name as $record){
        //     $nama = $record->nama;
        // }

        $nama = $org_name->isEmpty() ? '' : $org_name[0]->nama;
        $dishtype = DB::table('dish_type')->get();

        return view('orders.listmenu', compact('data','nama','organizationId','dishtype'));
    }

    public function addmenu($organizationId){
        //dd($organizationId);
        $data = DB::table('dish_type')->get();
        return view('orders.addmenu', compact('data','organizationId'));
    }

    public function processaddmenu(Request $request, $organizationId){
        $request->validate([
            'dishname' => 'required',
            'dishtype' => 'required',
            'price' => ['required', 'regex:/^\d{1,6}(\.\d{1,2})?$/'] // Matches double(8,2)
        ]);
    
        $dish = new Dish();
        $dish->name = $request->dishname;
        $dish->price = $request->price;
        $dish->organ_id = $organizationId;
        $dish->dish_type = $request->dishtype;
        $result = $dish->save();
    
        if($result)
        {
            return back()->with('success', 'Menu Berjaya Ditambah');
        }
        else
        {
            return back()->withInput()->with('error', 'Menu Gagal Ditambah');
        }
    }

    public function editmenu(Request $request){
        //dd($request->all());

        $request->validate([
            'dishname' => 'required',
            'dishtype' => 'required',
            'price' => ['required', 'regex:/^\d{1,6}(\.\d{1,2})?$/'] // Matches double(8,2)
        ]);
        
        $updatedRows = DB::table('dishes')
            ->where('id', $request->dishid)
            ->update([
                'name' => $request->dishname,
                'price' => $request->price,
                'dish_type' => $request->dishtype
            ]);
        
        if ($updatedRows) {
            return back()->with('success', 'Menu Berjaya Disunting');
        } else {
            return back()->withInput()->with('error', 'Menu Gagal Disunting');
        }        
    }

    public function uruspesanan(){
        $orgtype = 'OrderS';
        $userId = Auth::id();
        $data = DB::table('organizations as o')
        ->leftJoin('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->leftJoin('type_organizations as to', 'o.type_org', '=', 'to.id')
        ->select("o.*")
        ->distinct()
        ->where('to.nama', $orgtype)
        ->where('ou.user_id',$userId)
        ->get();

        return view('orders.uruspesanan', compact('data'));
    }

    public function listpesanan($organizationId){
        $userId = Auth::id();
        $data = Order::join('order_dish as od', 'od.order_id', '=', 'orders.id')
        ->join('dish_available as da', 'da.id', '=', 'orders.dish_available_id')
        ->join('dishes as d', 'd.id', '=', 'od.dish_id') // Corrected 'order_dish' to 'od'
        ->where('orders.organ_id', $organizationId)
        ->select('orders.id', 'd.name as dishname', 'od.quantity', 'da.date', 'da.time', 'da.delivery_address', 'orders.delivery_status')
        ->groupBy('orders.id', 'd.name', 'od.quantity', 'da.date', 'da.time', 'da.delivery_address', 'orders.delivery_status') // Added missing columns in GROUP BY
        ->get();

        $org_name = Organization::where('id',$organizationId)
        ->select('nama')
        ->get();

        $nama = $org_name->isEmpty() ? '' : $org_name[0]->nama;

        return view('orders.listpesanan', compact('data','nama','organizationId'));
    }

    public function editpesanan(Request $request){
        //dd($request->all());

        $request->validate([
            'status' => 'required'
        ]);
        
        $updatedRows = DB::table('orders')
            ->where('id', $request->orderid)
            ->update([
                'delivery_status' => $request->status
            ]);
        
        if ($updatedRows) {
            return back()->with('success', 'Status Berjaya Disunting');
        } else {
            return back()->withInput()->with('error', 'Status Gagal Disunting');
        } 
    }
    
    public function laporanjualan(){
        $orgtype = 'OrderS';
        $userId = Auth::id();

        $data = DB::table('organizations as o')
            ->leftJoin('organization_user as ou', 'o.id', 'ou.organization_id')
            ->leftJoin('type_organizations as to', 'o.type_org', 'to.id')
            ->select("o.*")
            ->distinct()
            ->where('ou.user_id', $userId)
            ->where('to.nama', $orgtype)
            ->where('o.deleted_at', null)
            ->get();

        return view('orders.laporanjualan', compact('data'));
    }

    public function salesreport($id,$start,$end){
        //dd($id,$start,$end);
        $checkinTimestamp = strtotime($start);
        $checkoutTimestamp = strtotime($end);
        
        $salesData = DB::table('order_dish')
            ->join('orders', 'orders.id', '=', 'order_dish.order_id')
            ->join('dishes', 'dishes.id', '=', 'order_dish.dish_id')
            ->select(DB::raw('DATE(order_dish.updated_at) as date'), DB::raw('SUM(order_dish.quantity*dishes.price) as total_sales'))
            //->where('order.transaction_id', null)
            // ->whereNotNull('order.transaction_id')
            ->where('orders.organ_id', $id)
            ->whereBetween(DB::raw('DATE(order_dish.updated_at)'), [date('Y-m-d', $checkinTimestamp), date('Y-m-d', $checkoutTimestamp)])
            ->groupBy(DB::raw('DATE(order_dish.updated_at)'))
            ->get();
        
        $dateLabels = [];
        $currentDate = $checkinTimestamp;
        while ($currentDate <= $checkoutTimestamp) {
            $dateLabels[] = date('Y-m-d', $currentDate);
            $currentDate += 86400;
        }
        
        $dailySales = [];
        foreach ($dateLabels as $dateLabel) {
            $found = false;
            foreach ($salesData as $entry) {
                if ($entry->date === $dateLabel) {
                    $dailySales[] = $entry->total_sales;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $dailySales[] = 0; // No sales for this date
            }
        }
        
        // Prepare the data for the chart
        $chartData = [
            'labels' => $dateLabels,
            'dataset' => $dailySales,
        ];
        
        return response()->json($chartData);
    }

    public function buatpesanan(){
        $orgtype = 'OrderS';
        $data = DB::table('organizations as o')
        ->leftJoin('type_organizations as to', 'o.type_org', '=', 'to.id')
        ->select("o.*")
        ->distinct()
        ->where('to.nama', $orgtype)
        ->get();

        return view('orders.pesanan', compact('data'));
    }

    public function pilihlokasi($organizationId){
        //dd($organizationId);
        $dishes = DB::table('dishes')
        ->where('organ_id', $organizationId)
        ->get();

        $latitudeLongitude = "2.3138° N, 102.3211° E";
        list($latitude, $longitude) = sscanf($latitudeLongitude, "%f° N, %f° E");

        // Pass the converted latitude and longitude to the view
        $apiKey = config('app.google_maps_api_key');
        $zoom = 15;

        return view('orders.pilihlokasi', compact('dishes', 'apiKey', 'latitude', 'longitude', 'zoom','organizationId'));
    }

    public function addorder(Request $request, $organizationId){
        //dd($request->all());

        $userId = Auth::id();

        $status = 'Pending';

        $latitudeLongitude = "2.3138° N, 102.3211° E";
        list($latitude, $longitude) = sscanf($latitudeLongitude, "%f° N, %f° E");

        $request->validate([
            'address' => 'required',
            'dish' => 'required',
            'quantity' => 'required',
            'delivery_date' => 'required',
            'delivery_time' => 'required'
        ]);
    
        $delivery = new Dish_Available();
        $delivery->date = $request->input('delivery_date');
        $delivery->time = $request->input('delivery_time');
        $delivery->delivery_address = $request->input('address');
        $delivery->dish_id = $request->input('dish');
        $delivery->latitude = $latitude;
        $delivery->longitude = $longitude;
        $result = $delivery->save();
    
        if($result)
        {
            $deliveryId = DB::getPdo()->lastInsertId();

            $order = new Order();
            $order->delivery_status = $status;
            $order->user_id = $userId;
            $order->organ_id = $organizationId;
            $order->dish_available_id = $deliveryId;
            $order->order_description = $request->input('description');
            $result2 = $order->save();

            if($result2)
            {
                $orderId = DB::getPdo()->lastInsertId();

                $orderDish = new Order_Dish();
                $orderDish->quantity = $request->input('quantity');
                $orderDish->order_id = $orderId;
                $orderDish->dish_id = $request->input('dish');
                $result3 = $orderDish->save();

                if($result3)
                {
                    //return back()->with('success', 'Pesanan Berjaya Dihantar');

                    $dishes = DB::table('dishes')
                    ->where('organ_id', $organizationId)
                    ->get();

                    return view('orders.extraorder', compact('organizationId', 'orderId', 'dishes'));
                }
                else
                {
                    return back()->withInput()->with('error', 'Pesanan Gagal Dihantar (OrderDish)');
                }
            }
            else
            {
                return back()->withInput()->with('error', 'Pesanan Gagal Dihantar (Order)');
            }
        }
        else
        {
            return back()->withInput()->with('error', 'Pesanan Gagal Dihantar (Delivery)');
        }
    }

    public function extraorder(Request $request, $organizationId, $orderId){
        $orderDish = new Order_Dish();
        $orderDish->quantity = $request->input('quantity');
        $orderDish->order_id = $orderId;
        $orderDish->dish_id = $request->input('dish');
        $result = $orderDish->save();

        if($result)
        {
            //return back()->with('success', 'Pesanan Berjaya Dihantar');

            $dishes = DB::table('dishes')
            ->where('organ_id', $organizationId)
            ->get();

            return view('orders.extraorder', compact('organizationId', 'orderId', 'dishes'));
        }
        else
        {
            return back()->withInput()->with('error', 'Pesanan Gagal Dihantar');
        }
    }

    public function checkout($orderId){
        //dd($orderId);

        $userId = Auth::id();
        $data = Organization::join('orders', 'organizations.id', '=', 'orders.organ_id')
            ->join('order_dish','order_dish.order_id','=','orders.id')
            ->join('dishes','dishes.id','=','order_dish.dish_id')
            ->where('orders.user_id', $userId)
            ->where('orders.id',$orderId)
            ->select('organizations.nama', 'organizations.address', 'dishes.name', 'order_dish.quantity', 'dishes.price','order_dish.updated_at', DB::raw('SUM(order_dish.quantity*dishes.price) as totalprice'))
            ->get();

        // $sum = 0;
        // $totalprice = 0;

        // foreach ($data as $record) {
        //     $sum = $record->price * $record->quantity;
        //     $totalprice += $sum;
        // }

        //return view('homestay.homestayresit',compact('data','bookingid','totalprice'));
        return view('orders.checkout', compact('orderId','data'));
    }

    public function trackorder(){
        $userId = Auth::id();

        $data = DB::table('dishes')
            ->join('order_dish', 'dishes.id', '=', 'order_dish.dish_id')
            ->join('orders', 'orders.id', '=', 'order_dish.order_id')
            ->where('orders.user_id', $userId)
            ->select('dishes.name', 'order_dish.quantity', 'orders.delivery_status','orders.order_description','orders.updated_at')
            ->get();    

        return view('orders.trackorder',compact('data'));
    }

    public function testData(Request $request){
        $data = DB::table('users as u')
            ->where('name', 'SALAM BIN ISNIN')
            ->get();
        return response()->json($data);
        //return $data;
        //return response()->json(['name' => "SALAM"]);
    }

    public function login(Request $request)
    {  
       $credentials = $request->only('email', 'password');
       $phone = $request->get('email');
       //return response()->json(['user',$credentials],200);
       if(is_numeric($request->get('email'))){
           $user = User::where('icno', $phone)->first();
          
           if ($user) {
               //dd($user);
               //return ['icno' => $phone, 'password' => $request->get('password')];
               $credentials = ['icno'=>$phone, 'password' => $request->get('password')];
           }
           else{
               if(!$this->startsWith((string)$request->get('email'),"+60") && !$this->startsWith((string)$request->get('email'),"60")){
                   if(strlen((string)$request->get('email')) == 10)
                   {
                       $phone = str_pad($request->get('email'), 12, "+60", STR_PAD_LEFT);
                   } 
                   elseif(strlen((string)$request->get('email')) == 11)
                   {
                       $phone = str_pad($request->get('email'), 13, "+60", STR_PAD_LEFT);
                   }   
               } else if($this->startsWith((string)$request->get('email'),"60")){
                   if(strlen((string)$request->get('email')) == 11)
                   {
                       $phone = str_pad($request->get('email'), 12, "+", STR_PAD_LEFT);
                   } 
                   elseif(strlen((string)$request->get('email')) == 12)
                   {
                       $phone = str_pad($request->get('email'), 13, "+", STR_PAD_LEFT);
                   }   
               }
               $credentials = ['telno'=>$phone,'password'=>$request->get('password')];
           }
       }
       else if(strpos($request->get('email'), "@") !== false){
           $credentials = ['email'=>$phone,'password'=>$request->get('password')];
       }
       else{
           $credentials =['telno' => $phone, 'password'=>$request->get('password')];

       }


       if (Auth::attempt($credentials)) {
           $user = Auth::User();

           $organization_user = DB::table('organization_user as ou')
            ->where('ou.user_id', $user->id)
            ->where('ou.role_id', 17)
            ->get();


            if(count($organization_user) > 0) {
                //if user is orders admin
                $exist = 1;
            } else {
                $exist = 0;
            }

           return response()->json([
               'id' => $user->id,
               'name' => $user->name,
               'exist' => $exist
           ], 200);
       }
       return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    public function isUserOrderSAdmin(Request $request) {
        $user_id = $request->get('user_id');

        $data = DB::table('organization_user as ou')
            ->where('ou.user_id', $user_id)
            ->where('ou.role_id', 17)
            ->get();

        // if(count($data) > 0) {
        //     //if user is orders admin
        //     return response()->json(['respo' => 'admin']);
        // } else {
        //     return response()->json(['respo' => 'not admin']);
        // }
        return response()->json($data);
    }

    public function randomDishes() {
        $data = DB::table('dishes as d')
            ->join('organizations as o', 'd.organ_id', '=', 'o.id')
            ->select('d.*', 'o.nama as o_nama')
            ->inRandomOrder()
            ->limit(1)
            ->get();

            return response()->json($data);
    }

    public function listDishes(){
        $data = DB::table('dishes as d')
            ->join('organizations as o', 'd.organ_id', '=', 'o.id')
            ->select('d.*', 'o.nama as o_nama')
            ->get();

        return response()->json($data);
    }

    public function listShops(){
        $data = DB::table('organizations as o')
            ->where('type_org', 8) //kedai makanan
            ->orWhere('type_org', 12) //OrderS
            //->where('nama', 'like', '%MAAHAD TAHFIZ SAINS DARUL AMAN%')
            //->limit(30)
            ->get();

        return response()->json($data);  
    }

    public function listDishesByShop(Request $request) {
        $org_id = $request->get('organ_id');
        
        $data = DB::table('dishes as d')
            ->join('organizations as o', 'd.organ_id', '=', 'o.id')
            ->where('o.id', $org_id)
            ->select('d.*')
            ->get();

        $count = DB::table('dishes')    
                    ->select('dishes.id', 'dishes.name', DB::raw('COUNT(order_available.dish_id) as totalOrderAvailable'))
                    ->leftJoin('order_available', 'dishes.id', '=', 'order_available.dish_id')
                    ->where('dishes.organ_id', $org_id)
                    //->where('order_available.quantity', '>', 0)
                    ->groupBy('dishes.id', 'dishes.name')
                    ->orderBy('dishes.id')
                    ->get();

        // return response()->json($data);
        return response()->json(['data'=>$data,'count'=>$count]);
    }

    public function listDishAvailable(Request $request) {
        $dish_id = $request->get('dish_id');

        // $dateList = DB::table('dish_available as da')
        //     ->distinct()
        //     ->select('da.date')
        //     ->where('da.dish_id', $dish_id)
        //     ->get();

        $data = DB::table('dish_available as da')
            ->where('da.dish_id', $dish_id)
            ->get();
            
        return response()->json($data);  
        // return response()->json(['dateList'=>$dateList,'data'=>$data]);  
    }

    public function listOrderAvailable(Request $request) {
        $organ_id = $request->get('organ_id');

        $data = DB::table('order_available as oa')
            ->select('oa.*')
            ->join('dishes as d', 'd.id', '=', 'oa.dish_id')
            ->where('d.organ_id', $organ_id)
            ->where('oa.quantity', '>', 0)
            // ->where('oa.dish_id', $dish_id)
            ->get();
            
        return response()->json($data);  
    }

    public function getOrderCart(Request $request) {
        $user_id = $request->get('user_id');
        $organ_id = $request->get('organ_id');

        $cart = DB::table('order_cart')
                    ->where('order_status', 'checkout-pending')
                    ->where('user_id', $user_id)
                    ->where('organ_id', $organ_id)
                    ->first();

        // $cart_id = $cart->id;
        // dd($cart_id);
        
        // if($cart->isEmpty()) {
        if(!$cart) {
            $cart_id = DB::table('order_cart')->insertGetId([
                'order_status' => 'checkout-pending',
                'totalamount' => 0,
                'created_at' => now(),
                'user_id' => $user_id,
                'organ_id' => $organ_id,
            ]);

            $cart = DB::table('order_cart')
                    ->where('id', $cart_id)
                    ->first();
        }

        $order_available_dish = DB::table('order_available_dish as oad')
                                    ->where('order_cart_id', $cart->id)
                                    ->get();

        // return response()->json($order_available_dish);
        return response()->json(['order_available_dish'=>$order_available_dish,'cart'=>$cart]);
    }

    public function createOrderCart(Request $request) {
        $quantity = $request->get('quantity');
        $totalprice = $request->get('totalprice');
        $order_available_id = $request->get('order_available_id');
        $order_cart_id = $request->get('order_cart_id');

        if(count($request->all()) >= 1) {
            DB::table('order_cart')
            ->where('id', $order_cart_id)
            ->update([
                'order_status' => 'order-pending',
                'created_at' => now(),
            ]);

            DB::table('order_available')
            ->where('id', $order_available_id)
            ->decrement('quantity', $quantity);

            DB::table('order_available_dish')->insert([
                'quantity' => $quantity,
                'totalprice' => $totalprice,
                'delivery_status' => 'order-pending',
                'order_available_id' => $order_available_id,
                'order_cart_id' => $order_cart_id
            ]);
            return response()->json(['response' => 'Order Created Successfully']);
        } else {
            return response()->json(['response' => 'Order Failed']);
        }
    }

    public function getOrderAvailableDish(Request $request) {
        $user_id = $request->get('user_id');
        $option = $request->get('option');

        // $data = DB::table('order_available_dish as oad')
        //             ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
        //             ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
        //             ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
        //             ->where('oc.user_id', '=', $user_id)
        //             ->where('oa.delivery_date', '>', now())
        //             ->select('oad.* as order_available_dish', 'oc.* as order_cart', 'oa.* as order_available', 'd.* as dishes')
        //             ->get();

        if(count($request->all()) >= 1) {

            if($option == 0) {
                $order_available_dish = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '>', now())
                    ->select('oad.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $order_cart = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '>', now())
                    ->select('oc.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $order_available = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '>', now())
                    ->select('oa.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $dishes = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '>', now())
                    ->select('d.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $organizations = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->join('organizations as o', 'o.id', '=', 'oc.organ_id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '>', now())
                    ->select('o.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();
                    
            } else if($option == 1) {
                $order_available_dish = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '<', now())
                    ->select('oad.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $order_cart = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '<', now())
                    ->select('oc.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $order_available = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '<', now())
                    ->select('oa.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $dishes = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '<', now())
                    ->select('d.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();

                $organizations = DB::table('order_available_dish as oad')
                    ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                    ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                    ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                    ->join('organizations as o', 'o.id', '=', 'oc.organ_id')
                    ->where('oc.user_id', '=', $user_id)
                    ->where('oa.delivery_date', '<', now())
                    ->select('o.*')
                    ->orderBy('oa.delivery_date', 'asc')
                    ->get();
            }
        }
        
        return response()->json(['order_available_dish'=>$order_available_dish, 'order_cart'=>$order_cart, 'order_available'=>$order_available, 'dishes'=>$dishes, 'organizations'=>$organizations]);
        // return response()->json($order_available_dish);
    }

    
}
