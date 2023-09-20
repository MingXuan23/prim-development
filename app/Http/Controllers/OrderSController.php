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
        $userId = Auth::id();
        $data = Organization::where('type_org', '12')->get();
        return view('orders.menu', compact('data'));
    }

    public function listmenu($organizationId){
        dd($organizationId);
    }

    //old
    public function dashboard(Request $request){
        $data = array();
        $organizations = Organization::all();

        $organizationId = $request->input('organization_id');
        $dishes = Dish::where('organization_id', $organizationId)->get();

        $latitudeLongitude = "2.3138° N, 102.3211° E";
        list($latitude, $longitude) = sscanf($latitudeLongitude, "%f° N, %f° E");

        // Pass the converted latitude and longitude to the view
        $apiKey = config('app.google_maps_api_key');
        $zoom = 15;

        if(Session::has('loginId')){
            $data = User::where('user_id','=', Session::get('loginId'))->first();
        }
        return view('dashboard', compact('data','apiKey', 'latitude', 'longitude', 'zoom','organizations','dishes'));
    }

    public function storeOrders(Request $request)
    {
        // dd($request->all());

        $data = array();
        if(Session::has('loginId')){
            $data = User::where('user_id','=', Session::get('loginId'))->first();
        }

        // $organizationId = '';
        $organizationId = $request->input('organization_id');

        DB::beginTransaction();
        $deliveryDates = $request->input('delivery_date');
        // $organizationId = $request->input('organization_id');

        try {
            foreach ($deliveryDates as $index => $date) {
                // Create Delivery
                $delivery = new Delivery();
                $delivery->date = $date;
                $delivery->time = $request->input('delivery_time');
                $delivery->delivery_address = $request->input('delivery_address');
                // $delivery->dish_id = $dishIds[$index];
                $delivery->dish_id = $request->input('dish_id')[$index];
                $delivery->latitude = $request->input('latitude');
                $delivery->longitude = $request->input('longitude');
                $delivery->save();

                // Retrieve the generated delivery_id
                // $request->session()->put('deliveryId', $delivery->delivery_id);
                $deliveryId = DB::getPdo()->lastInsertId();

                // Create Order
                $order = new Order();
                $order->delivery_status = $request->input('delivery_status');
                $order->user_id = Session::get('loginId');
                $order->organization_id = $organizationId;
                $order->delivery_id = $deliveryId;
                $order->order_description = $request->input('order_description');
                $order->save();

                // Retrieve the generated order_id
                // $request->session()->put('orderId', $order->order_id);
                $orderId = DB::getPdo()->lastInsertId();

                // Create Order Dish
                $orderDish = new Order_Dish();
                $orderDish->quantity = $request->input('dish_quantity')[$index]; // Use the correct index
                $orderDish->order_id = $orderId; // Use the correct order ID
                $orderDish->dish_id = $request->input('dish_id')[$index]; // Use the correct index
                $orderDish->save();
            }

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create Order(s)');
        }
        
        
        // After the if-elseif block, fetch the organization data based on $organizationId
        $organizations = DB::table('organizations')->where('organization_id', $organizationId)->first();
        // dd($organizationId);
        // Retrieve the generated order_id
        // $request->session()->put('orderId', $order->order_id);
        $orderId = DB::getPdo()->lastInsertId();

        // Pass data to the view
        return view('order', compact('data', 'orderId', 'organizationId', 'organizations'));
    }

    public function addOrders(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Retrieve the generated order_id
            // $request->session()->put('orderId', $order->order_id);
            $orderId = DB::getPdo()->lastInsertId();

            // Create Order Dish
            $orderDish = new Order_Dish();
            $orderDish->quantity = $request->input('dish_quantity')[$index]; // Use the correct index
            $orderDish->order_id = $orderId; // Use the correct order ID
            $orderDish->dish_id = $request->input('dish_id')[$index]; // Use the correct index
            $orderDish->save();

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create Order(s)');
        }

        // After the if-elseif block, fetch the organization data based on $organizationId
        $organizations = DB::table('organizations')->where('organization_id', $organizationId)->first();
        // dd($organizationId);
        // Retrieve the generated order_id
        // $request->session()->put('orderId', $order->order_id);
        $orderId = DB::getPdo()->lastInsertId();

        // Pass data to the view
        return view('order', compact('data', 'orderId', 'organizationId', 'organizations'));
    }

    public function checkoutOrders()
    {

    }

    public function getDishesByOrganization($organizationId)
    {
        $dishes = Dish::where('organization_id', $organizationId)->get();
        return response()->json($dishes);
    }

    public function trackorder()
    {
        $data = array();
        if(Session::has('loginId')){
            $data = User::where('user_id','=', Session::get('loginId'))->first();
        }

        $OrdersAndDish = DB::table('dishes')
            ->join('order__dishes', 'dishes.dish_id', '=', 'order__dishes.dish_id')
            ->join('orders', 'orders.order_id', '=', 'order__dishes.order_id')
            ->select('dishes.name', 'order__dishes.quantity', 'orders.delivery_status','orders.order_description','orders.created_at')
            ->get();    

        return view('trackOrder',compact('data','OrdersAndDish'));
    }

    public function admindashboard(Request $request){
        $organizations = Organization::all();

        $selectedOrganization = $request->input('organization_id');
        $selectedYear = $request->input('year', Carbon::now()->year);

        $salesData = Order_Dish::selectRaw('dishes.name as dish, SUM(order__dishes.quantity) as total_quantity, MONTH(orders.created_at) as month')
            ->join('orders', 'order__dishes.order_id', '=', 'orders.order_id')
            ->join('dishes', 'order__dishes.dish_id', '=', 'dishes.dish_id')
            ->where('orders.organization_id', $selectedOrganization)
            ->whereYear('orders.created_at', $selectedYear)
            ->groupBy('dishes.name', 'month')
            ->get();

        return view('admin.dashboard', compact('organizations','salesData', 'selectedOrganization', 'selectedYear'));
    }

    public function adminaddorganization(){
        return view('admin.addOrganization');
    }

    public function addOrganization(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:organizations',
            'address'=>'required',
            'postcode'=>'required',
            'state'=>'required'
        ]);

        $organization = new Organization();
        $organization->email = $request->email;
        $organization->name = $request->name;
        $organization->address = $request->address;
        $organization->postcode = $request->postcode;
        $organization->state = $request->state;
        $res = $organization->save();

        if($res){
            return back()->with('success','Organization successfully added');
        }
        else{
            return back()->with('fail','Organization creation fail');
        }
    }

    public function adminadddishes(){
        $organizations = Organization::all();
        $type = Dish_Type::all();

        return view('admin.addDishes', compact('organizations','type'));
    }

    public function addDishes(Request $request){
        $request->validate([
            'name' => 'required',
            'price' => ['required', 'regex:/^\d{1,6}(\.\d{1,2})?$/'], // Matches double(8,2)
            'organization' => 'required',
            'type' => 'required'
        ]);        

        $dish = new Dish();
        $dish->name = $request->name;
        $dish->price = $request->price;
        $dish->organization_id = $request->organization;
        $dish->dish_type_id = $request->type;
        $res = $dish->save();

        if($res){
            return back()->with('success','Dish successfully added');
        }
        else{
            return back()->with('fail','Dish creation fail');
        }
    }

}
