<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\PickUpOrder;
use App\Models\ProductItem;
use App\Models\ProductType;
use App\Models\ProductOrder;
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

        $product_item = ProductItem::with(['product_type' => function($q) use ($id){
            $q->where('product_type.organization_id', $id);
        }])
        ->orderBy('name')
        ->get();

        $product_type = ProductType::where('organization_id', $id)
        ->get();

        $jenis = array();
        foreach($product_item as $item)
        {
            foreach($product_type as $type)
            {
                if($item->product_type_id == $type->id)
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
}
