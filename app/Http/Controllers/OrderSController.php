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
       $device_token = $request->get('device_token');
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

            $user_device_token = DB::table('users')
                ->where('id', $user->id)
                ->select('device_token')
                ->first();

            if($user_device_token) {
                if($device_token != $user_device_token) {
                    DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'device_token' => $device_token
                    ]);

                    DB::table('users')
                    ->where('id', '!=', $user->id)
                    ->where('device_token', $device_token)
                    ->update([
                        'device_token' => null
                    ]);
                }
            } else {
                DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'device_token' => $device_token
                ]);

                DB::table('users')
                ->where('id', '!=', $user->id)
                ->where('device_token', $device_token)
                ->update([
                    'device_token' => null
                ]);
            }

            
           $organizations = DB::table('organization_user as ou')
            ->join('organizations as o', 'ou.organization_id', '=', 'o.id')
            ->where('ou.user_id', $user->id)
            ->where('ou.role_id', 17)
            ->select('o.*')
            ->first();

        //dd($organization_user);
           return response()->json([
               'id' => $user->id,
               'name' => $user->name,
               'email' => $user->email,
               'icno' => $user->icno,
               'telno' => $user->telno,
               'username' => $user->username,
               'device_token' => $user->device_token,
               'organizations' => $organizations
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

    public function logout(Request $request) {
        $user_id = $request->get('user_id');

        if(count($request->all()) >= 1) {
            DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'device_token' => null
            ]);

            return response()->json(['response' => 'Log Out Successfully']);
        } else {
            return response()->json(['response' => 'Log Out Failed']);
        }
    }

    public function updateUser(Request $request) {
        $user_id = $request->get('user_id');
        $name = $request->get('name');
        $telno = $request->get('telno');
        $email = $request->get('email');

        if(count($request->all()) >= 1) {
            $user = DB::table('users')
                ->where('id', $user_id)
                ->update([
                    'name' => $name,
                    'telno' => $telno,
                    'email' => $email
            ]);

            return response()->json(['response' => 'Updated Successfully']);
        } else {
            return response()->json(['response' => 'Update Failed']);
        }
    }

    public function updateOrganization(Request $request) {
        $organ_id = intval($request->organ_id);
        $nama = $request->nama;
        $address = $request->address;
        $city = $request->city;
        $district = $request->district;
        $postcode = $request->postcode;
        $state = $request->state;

        $file_name = '';
        $resp = 'Successfully updated';

        if (!is_null($request->file('organization_pic'))) {
            // Check if there is an existing image
            $existingImage = DB::table('organizations')
                ->where('id', $organ_id)
                ->whereNotNull('organization_picture')
                ->where('organization_picture', '<>', '')
                ->first();

            // Delete the existing image
            if ($existingImage) {
                $existingImagePath = public_path('organization-picture') . '/' . $existingImage->organization_picture;
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
            }

            // $storagePath  = $request->dish_image->storeAs('public/orders-asset/dish-image', 'dish-image-'.time(). '-' . $organ_id . '.jpg');
            // $file_name = basename($storagePath);
            $resp = 'Successfully updated with image'; 
            $extension = $request->file('organization_pic')->extension();
            $storagePath = $request->file('organization_pic')->move(public_path('organization-picture'), 'organization-picture-' . time(). '-' . $organ_id . '.' . $extension);
            $file_name = basename($storagePath);
        }

        if(count($request->all()) >= 1) {

            $image_exist = DB::table('organizations')
                ->where('id', $organ_id)
                ->whereNotNull('organization_picture')
                ->where('organization_picture', '<>', '')
                ->exists();

            if($image_exist) {
                if($file_name) {
                    DB::table('organizations')
                    ->where('id', $organ_id)
                    ->update([
                        'nama' => $nama,
                        'address' => $address,
                        'city' => $city,
                        'district' => $district,
                        'postcode' => $postcode,
                        'state' => $state,
                        'organization_picture' => $file_name
                    ]);
                } else {
                    DB::table('organizations')
                    ->where('id', $organ_id)
                    ->update([
                        'nama' => $nama,
                        'address' => $address,
                        'city' => $city,
                        'district' => $district,
                        'postcode' => $postcode,
                        'state' => $state,
                    ]);
                }
            } else {
                DB::table('organizations')
                ->where('id', $organ_id)
                ->update([
                    'nama' => $nama,
                    'address' => $address,
                    'city' => $city,
                    'district' => $district,
                    'postcode' => $postcode,
                    'state' => $state,
                    'organization_picture' => $file_name
                ]);
            }

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Update Failed']);
        }
    }

    public function randomDishes() {
        $data = DB::table('dishes as d')
            ->join('organizations as o', 'd.organ_id', '=', 'o.id')
            ->select('d.*', 'o.nama as o_nama')
            ->Where('o.type_org', 12) //OrderS
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
            //->where('type_org', 8) //kedai makanan
            //->orWhere('type_org', 12) //OrderS
            ->Where('type_org', 12) //OrderS
            //->where('nama', 'like', '%MAAHAD TAHFIZ SAINS DARUL AMAN%')
            //->limit(30)
            ->get();

        return response()->json($data);  
    }

    public function listDishesByShop(Request $request) {
        $org_id = $request->get('organ_id');
        
        // $data = DB::table('dishes as d')
        //     ->join('organizations as o', 'd.organ_id', '=', 'o.id')
        //     ->where('o.id', $org_id)
        //     ->select('d.*')
        //     ->get();

        $data = DB::table('dishes')    
                    ->select('dishes.*', DB::raw('COUNT(order_available.dish_id) as totalOrderAvailable'))
                    ->leftJoin('order_available', 'dishes.id', '=', 'order_available.dish_id')
                    ->where('dishes.organ_id', $org_id)
                    ->where('order_available.delivery_date', '>', now())
                    //->where('order_available.quantity', '>', 0)
                    ->groupBy('dishes.id', 'dishes.name')
                    ->orderBy('dishes.id')
                    ->get();

        // return response()->json($data);
        return response()->json($data);
    }

    public function listDishesByShopAdmin(Request $request) {
        $org_id = $request->get('organ_id');
        
        // $data = DB::table('dishes as d')
        //     ->join('organizations as o', 'd.organ_id', '=', 'o.id')
        //     ->where('o.id', $org_id)
        //     ->select('d.*')
        //     ->get();

        $data = DB::table('dishes')    
                    ->select('dishes.*', DB::raw('COUNT(order_available.dish_id) as totalOrderAvailable'))
                    ->leftJoin('order_available', 'dishes.id', '=', 'order_available.dish_id')
                    ->where('dishes.organ_id', $org_id)
                    //->where('order_available.quantity', '>', 0)
                    ->groupBy('dishes.id', 'dishes.name')
                    ->orderBy('dishes.id')
                    ->get();

        // return response()->json($data);
        return response()->json($data);
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
            ->where('oa.delivery_date', '>', now())
            // ->where('oa.dish_id', $dish_id)
            ->get();
            
        return response()->json($data);  
    }

    public function getOrderCart(Request $request) {
        $user_id = $request->get('user_id');
        $organ_id = $request->get('organ_id');

        $cart = DB::table('order_cart')
            ->where('order_status', 'checkout-cart-pending')
            //->orWhere('order_status', 'checkout-cart-pending-payment')
            ->where('user_id', $user_id)
            ->where('organ_id', $organ_id)
            ->first();


        // $cart_id = $cart->id;
        // dd($cart_id);
        
        // if($cart->isEmpty()) {
        if(!$cart) {
            $cart_id = DB::table('order_cart')->insertGetId([
                'order_status' => 'checkout-cart-pending',
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
        $total_amount = $request->get('total_amount');

        if(count($request->all()) >= 1) {
            // DB::table('order_available')
            // ->where('id', $order_available_id)
            // ->decrement('quantity', $quantity);

            DB::table('order_available_dish')->insert([
                'quantity' => $quantity,
                'totalprice' => $totalprice,
                'delivery_status' => 'order-pending',
                'order_available_id' => $order_available_id,
                'order_cart_id' => $order_cart_id
            ]);

            DB::table('order_cart')
            ->where('id', $order_cart_id)
            ->update([
                'order_status' => 'checkout-cart-pending-payment',
                'created_at' => now(),
                'totalamount' => $total_amount
            ]);

            return response()->json(['response' => 'Order Created Successfully | Waiting For Payment']);
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

            $data = DB::table('order_available_dish as oad')
                ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
                ->join('order_available as oa', 'oad.order_available_id', '=', 'oa.id')
                ->join('dishes as d', 'oa.dish_id', '=', 'd.id')
                ->join('organizations as o', 'o.id', '=', 'oc.organ_id')
                ->where('oc.user_id', $user_id);

            $order_available_dish = $data->select('oad.*')->get();
            $order_cart = $data->select('oc.*')->get();
            $order_available = $data->select('oa.*')->get();
            $dishes = $data->select('d.*')->get();
            $organizations = $data->select('o.*')->get();

            if($option == 0) {
                $order_available_dish = $data->select('oad.*')->where('oa.delivery_date', '>', now())->orderBy('oa.delivery_date', 'asc')->get();
                $order_cart = $data->select('oc.*')->where('oa.delivery_date', '>', now())->orderBy('oa.delivery_date', 'asc')->get();
                $order_available = $data->select('oa.*')->where('oa.delivery_date', '>', now())->orderBy('oa.delivery_date', 'asc')->get();
                $dishes = $data->select('d.*')->where('oa.delivery_date', '>', now())->orderBy('oa.delivery_date', 'asc')->get();
                $organizations = $data->select('o.*')->where('oa.delivery_date', '>', now())->orderBy('oa.delivery_date', 'asc')->get();
                    
            } else if($option == 1) {
                $order_available_dish = $data->select('oad.*')->where('oa.delivery_date', '<', now())->orderBy('oa.delivery_date', 'desc')->get();
                $order_cart = $data->select('oc.*')->where('oa.delivery_date', '<', now())->orderBy('oa.delivery_date', 'desc')->get();
                $order_available = $data->select('oa.*')->where('oa.delivery_date', '<', now())->orderBy('oa.delivery_date', 'desc')->get();
                $dishes = $data->select('d.*')->where('oa.delivery_date', '<', now())->orderBy('oa.delivery_date', 'desc')->get();
                $organizations = $data->select('o.*')->where('oa.delivery_date', '<', now())->orderBy('oa.delivery_date', 'desc')->get();
            }
        }
        
        return response()->json(['order_available_dish'=>$order_available_dish, 'order_cart'=>$order_cart, 'order_available'=>$order_available, 'dishes'=>$dishes, 'organizations'=>$organizations]);
        // return response()->json($order_available_dish);
    }

    public function getDishType() {
        $data = DB::table('dish_type')->get();
        return response()->json($data);  
    }

    public function addDishes(Request $request) {
        $organ_id = intval($request->organ_id);
        $dish_name = $request->dish_name;
        $dish_price = doubleval($request->dish_price);
        $dish_type = intval($request->dish_type);
        //$dish_image = $request->get('dish_image');

        $file_name = '';
        $resp = 'Successfully added';

        if (!is_null($request->file('dish_image'))) {
            // $storagePath  = $request->dish_image->storeAs('public/orders-asset/dish-image', 'dish-image-'.time(). '-' . $organ_id . '.jpg');
            // $file_name = basename($storagePath);
            $resp = 'Successfully added with image'; 
            $extension = $request->file('dish_image')->extension();
            $storagePath = $request->file('dish_image')->move(public_path('dish-image'), 'dish-image-' . time(). '-' . $organ_id . '.' . $extension);
            $file_name = basename($storagePath);
        }

        if(count($request->all()) >= 1) {
            DB::table('dishes')->insert([
                'name' => $dish_name,
                'price' => $dish_price,
                'dish_image' => $file_name,
                'created_at' => now(),
                'organ_id' => $organ_id,
                'dish_type' => $dish_type
            ]);

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Add Failed']);
        }
    }

    public function updateDishes(Request $request) {
        $dish_id = intval($request->dish_id);
        $organ_id = intval($request->organ_id);
        $dish_name = $request->dish_name;
        $dish_price = doubleval($request->dish_price);
        $dish_type = intval($request->dish_type);
        //$dish_image = $request->get('dish_image');

        $file_name = '';
        $resp = 'Successfully updated';

        if (!is_null($request->file('dish_image'))) {
            // Check if there is an existing image
            $existingImage = DB::table('dishes')
                ->where('id', $dish_id)
                ->whereNotNull('dish_image')
                ->where('dish_image', '<>', '')
                ->first();

            // Delete the existing image
            if ($existingImage) {
                $existingImagePath = public_path('dish-image') . '/' . $existingImage->dish_image;
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
            }

            // $storagePath  = $request->dish_image->storeAs('public/orders-asset/dish-image', 'dish-image-'.time(). '-' . $organ_id . '.jpg');
            // $file_name = basename($storagePath);
            $resp = 'Successfully updated with image'; 
            $extension = $request->file('dish_image')->extension();
            $storagePath = $request->file('dish_image')->move(public_path('dish-image'), 'dish-image-' . time(). '-' . $organ_id . '.' . $extension);
            $file_name = basename($storagePath);
        }

        if(count($request->all()) >= 1) {

            $image_exist = DB::table('dishes')
                ->where('id', $dish_id)
                ->whereNotNull('dish_image')
                ->where('dish_image', '<>', '')
                ->exists();

            if($image_exist) {
                if($file_name) {
                    DB::table('dishes')
                    ->where('id', $dish_id)
                    ->update([
                        'name' => $dish_name,
                        'price' => $dish_price,
                        'dish_image' => $file_name,
                        'updated_at' => now(),
                        'organ_id' => $organ_id,
                        'dish_type' => $dish_type
                    ]);
                } else {
                    DB::table('dishes')
                    ->where('id', $dish_id)
                    ->update([
                        'name' => $dish_name,
                        'price' => $dish_price,
                        'updated_at' => now(),
                        'organ_id' => $organ_id,
                        'dish_type' => $dish_type
                    ]);
                }
            } else {
                DB::table('dishes')
                ->where('id', $dish_id)
                ->update([
                    'name' => $dish_name,
                    'price' => $dish_price,
                    'dish_image' => $file_name,
                    'updated_at' => now(),
                    'organ_id' => $organ_id,
                    'dish_type' => $dish_type
                ]);
            }

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Update Failed']);
        }
    }

    public function deleteDishes(Request $request) {
        $dish_id = $request->get('dish_id');

        $resp = 'Successfully deleted';

        if(count($request->all()) >= 1) {
            $existingImage = DB::table('dishes')
                ->where('id', $dish_id)
                ->whereNotNull('dish_image')
                ->where('dish_image', '<>', '')
                ->first();

            // Delete the existing image
            if ($existingImage) {
                $existingImagePath = public_path('dish-image') . '/' . $existingImage->dish_image;
                if (file_exists($existingImagePath)) {
                    unlink($existingImagePath);
                }
            }

            DB::table('dishes')
            ->where('id', $dish_id)
            ->delete();

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Delete Failed']);
        }
    }

    public function addOrderAvailable(Request $request) {
        $dish_id = $request->get('dish_id');
        $open_date = Carbon::createFromDate($request->get('open_date'));
        $close_date = Carbon::createFromDate($request->get('close_date'));
        $delivery_date = Carbon::createFromDate($request->get('delivery_date'));
        $delivery_address = $request->get('delivery_address');
        $quantity = $request->get('quantity');

        $resp = 'Successfully added';

        if(count($request->all()) >= 1) {
            DB::table('order_available')->insert([
                'open_date' => $open_date,
                'close_date' => $close_date,
                'delivery_date' => $delivery_date,
                'delivery_address' => $delivery_address,
                'quantity' => $quantity,
                'dish_id' => $dish_id
            ]);

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Add Failed']);
        }
    }

    public function updateOrderAvailable(Request $request) {
        $dish_id = $request->get('dish_id');
        $order_available_id = $request->get('order_available_id');
        $open_date = Carbon::createFromDate($request->get('open_date'));
        $close_date = Carbon::createFromDate($request->get('close_date'));
        $delivery_date = Carbon::createFromDate($request->get('delivery_date'));
        $delivery_address = $request->get('delivery_address');
        $quantity = $request->get('quantity');

        $resp = 'Successfully updated';

        if(count($request->all()) >= 1) {
            DB::table('order_available')
            ->where('id', $order_available_id)
            ->update([
                'open_date' => $open_date,
                'close_date' => $close_date,
                'delivery_date' => $delivery_date,
                'delivery_address' => $delivery_address,
                'quantity' => $quantity
            ]);

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Update Failed']);
        }
    }

    public function deleteOrderAvailable(Request $request) {
        $order_available_id = $request->get('order_available_id');

        $resp = 'Successfully deleted';

        if(count($request->all()) >= 1) {
            DB::table('order_available')
            ->where('id', $order_available_id)
            ->delete();

            return response()->json(['response' => $resp]);
        } else {
            return response()->json(['response' => 'Delete Failed']);
        }
    }

    public function listOrderAvailableAdmin(Request $request) {
        $dish_id = $request->get('dish_id');

        $order_available = DB::table('order_available as oa')
            ->select('oa.*', DB::raw('COUNT(oad.id) as totalOrderDishAvailable'))
            ->leftJoin('order_available_dish as oad', 'oad.order_available_id', '=', 'oa.id')
            ->join('dishes as d', 'd.id', '=', 'oa.dish_id')
            ->where('d.id', $dish_id)
            ->groupBy('oa.id')
            ->get();
            
        return response()->json($order_available);
    }

    public function listOADAdmin(Request $request) {
        $order_available_id = $request->get('order_available_id');

        $data = DB::table('order_available_dish as oad')
            ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
            ->join('users as u', 'u.id', '=', 'oc.user_id')
            ->where('oad.order_available_id', '=', $order_available_id);
        
        
        $order_available_dish = $data->select('oad.*')->get();
        $users = $data->select('u.*')->get();
        $order_cart = $data->select('oc.*')->get();
        

        return response()->json(['order_available_dish'=>$order_available_dish, 'users'=>$users, 'order_cart'=>$order_cart]);
    }

    public function updateOADAdmin(Request $request) {
        $order_available_dish_id = $request->get('order_available_dish_id');
        $order_cart_id = $request->get('order_cart_id');
        $delivery_status = $request->get('delivery_status');
        
        $update_OAD = DB::table('order_available_dish')
            ->where('id', $order_available_dish_id)
            ->update([
                'delivery_status' => $delivery_status
            ]);

        $total_Pending_Abandon = DB::table('order_cart as oc')
            ->join('order_available_dish as oad', 'oad.order_cart_id', '=', 'oc.id')
            ->where('oc.id', $order_cart_id)
            ->where('oad.delivery_status', 'order-preparing')
            ->count('oad.id');

        $order_cart = DB::table('order_cart')
            ->where('id', $order_cart_id)
            ->where('order_status', 'cart-overall-completed')
            ->first();

        if($order_cart) {
            DB::table('order_cart')
            ->where('id', $order_cart_id)
            ->update([
                'order_status' => 'cart-payment-completed',
                'updated_at' => now(),
            ]);
        }

        if($total_Pending_Abandon <= 0) {
            DB::table('order_cart')
            ->where('id', $order_cart_id)
            ->update([
                'order_status' => 'cart-overall-completed',
                'updated_at' => now(),
            ]);
        }
        
        if ($update_OAD) {
            return response()->json(['response' => 'Update successfully']);
        } else {
            return response()->json(['response' => 'Update failed']);
        }        
        
    }

    public function getUsers(Request $request) {
        $organ_id = $request->get('organ_id');

        $users = DB::table('users as u')
            ->join('organization_user as ou', 'ou.user_id', '=', 'u.id')
            ->where('ou.organization_id', $organ_id)
            ->where('ou.role_id', 17)
            ->first();

        return response()->json($users);
    }

    public function getReport(Request $request) {
        $organ_id = $request->get('organ_id');
        $date = Carbon::createFromDate($request->get('date'));
        $start_date = Carbon::createFromDate($request->get('start_date'));
        $end_date = Carbon::createFromDate($request->get('end_date'));

        $results = DB::table('dishes as d')
            ->join('order_available as oa', 'd.id', '=', 'oa.dish_id')
            ->join('order_available_dish as oad', 'oad.order_available_id', '=', 'oa.id')
            ->join('organizations as o', 'o.id', '=', 'd.organ_id')
            ->join('order_cart as oc', 'oc.id', '=', 'oad.order_cart_id')
            ->where('o.id', '=', $organ_id)
            ->groupBy('d.name')
            ->select('d.name as dish_name', DB::raw('SUM(oad.totalprice) as profit'), DB::raw('SUM(oad.quantity) as quantity'));

        if(!is_null($request->get('date'))) {
            $results = $results->whereDate('oc.created_at', $date)->get();
        } else if ((!is_null($request->get('start_date'))) || (!is_null($request->get('end_date')))) {
            $results = $results->whereBetween(DB::raw('DATE(oc.created_at)'), [$start_date, $end_date])->get();
        } else {
            $results = $results->get();
        }

        return response()->json($results);
    }

    public function mobilepayment(Request $request) {

        $order_cart_id = (int) $request->header('order_cart_id');
        $user_id = (int) $request->header('user_id');
        $organ_id = (int) $request->header('organ_id');
        $totalamount = (double) $request->header('totalamount');

        $order_cart = DB::table('order_cart')
            ->where('id', $order_cart_id)
            ->first();

        $users = DB::table('users')
            ->where('id', $user_id)
            ->first();

        $organizations = DB::table('organizations')
            ->where('id', $organ_id)
            ->first();

        //dd($order_cart, $users, $organizations, $totalamount);

        return view('orders.mobile.pay', compact('order_cart', 'users', 'organizations', 'totalamount'));
    }

    public function cartreceipt($transaction_id) {

        $transaction = DB::table('transactions')
                        ->where('id', $transaction_id)
                        ->first();

        $user = DB::table('users')
                    ->where('id', $transaction->user_id)
                    ->first();

        $order_cart = DB::table('order_cart')
                        ->where('transactions_id', $transaction->id)
                        ->first();

        $organization = DB::table('organizations')
                            ->where('id', $order_cart->organ_id)
                            ->first();

        $order_available_dish = DB::table('order_available_dish as oad')
                                    ->leftjoin('order_available as ou', 'oad.order_available_id', '=', 'ou.id')
                                    ->leftjoin('dishes as d', 'ou.dish_id', '=', 'd.id')
                                    ->where('oad.order_cart_id', $order_cart->id)
                                    ->select('*', 'oad.quantity as oad_quantity')
                                    ->get();

        // dd($transaction, $user, $order_cart, $organization, $order_available_dish);

        return view('orders.mobile.receipt', compact('transaction', 'user', 'order_cart', 'organization', 'order_available_dish'));
    }

    public function checkPaymentStatus(Request $request) {
        $organ_id = $request->get('organ_id');

        $users = DB::table('users as u')
            ->join('organization_user as ou', 'ou.user_id', '=', 'u.id')
            ->where('ou.organization_id', $organ_id)
            ->where('ou.role_id', 17)
            ->first();

        return response()->json($users);
    }
}
