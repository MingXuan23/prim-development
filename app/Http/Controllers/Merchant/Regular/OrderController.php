<?php

namespace App\Http\Controllers\Merchant\Regular;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\PgngOrder;
use App\Models\ProductItem;
use App\Models\ProductOrder;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index($id)
    {
        $todayDate = Carbon::now()->format('l');
        
        $day = RegularMerchantController::getDayIntegerByDayName($todayDate);

        $merchant = Organization::
        join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
        ->where([
            ['day', $day],
            ['organizations.id', $id]
        ])
        ->select('organizations.id as id', 'code', 'nama', 'address', 'postcode', 'state', 'city', 'fixed_charges',
        'day', 'open_hour', 'close_hour', 'status')
        ->first();

        $product_group = DB::table('product_group as pg')
        ->join('product_item as pi', 'pi.product_group_id', 'pg.id')
        ->where([
            // ['pi.status', 1],
            ['pg.organization_id', $id],
            ['pg.deleted_at', NULL],
            ['pi.deleted_at', NULL],
        ])
        ->select('pg.id', 'pg.name')
        ->distinct('pg.name')
        ->get();
            
        $product_item = DB::table('product_item as pi')
        ->join('product_group as pg', 'pg.id', 'pi.product_group_id')
        ->where([
            // ['pi.status', 1],
            ['pg.organization_id', $id],
            ['pg.deleted_at', NULL],
            ['pi.deleted_at', NULL],
        ])
        ->select('pi.id', 'pi.name', 'pi.desc', 'pi.price', 'pi.selling_quantity', 'pi.collective_noun', 'pi.image', 'pi.status', 'pi.product_group_id')
        ->orderBy('pi.product_group_id', 'asc')
        ->orderBy('pi.name')  
        ->get();

        $price = array();

        foreach($product_item as $row)
        {
            $price[$row->id] = number_format((double)(($row->price * $row->selling_quantity)), 2, '.', '');
        }

        return view('merchant.regular.menu', compact('merchant', 'product_group', 'product_item', 'price'));
    }

    public function fetchItem(Request $request)
    {
        $i_id = $request->get('i_id');
        $o_id = $request->get('o_id');
        $user_id = Auth::id();
        $modal = '';
        
        $item = ProductItem::where('id', $i_id)
        ->select('id', 'type', 'name', 'price', 'quantity_available as qty', 'selling_quantity as unit_qty')
        ->first();

        $order = DB::table('product_order as po')->join('pgng_orders as pu', 'pu.id', 'po.pgng_order_id')
        ->where([
            ['pu.user_id', $user_id],
            ['pu.organization_id', $o_id],
            ['po.product_item_id', $i_id],
            ['pu.status', 'In cart'],
        ])
        ->select('quantity as qty', 'selling_quantity as unit_qty')
        ->first();
        
        if($item->type == 'have inventory') {
            if($order) { // Order exists in cart
                $max_quantity = ($item->qty + ($order->qty * $order->unit_qty)); // (20 + (5 * 2)) = 
            } else {
                $max_quantity = $item->qty / $item->unit_qty;
            }
            
            $modal .= '<div class="row justify-content-center"><i>Kuantiti Inventori : '.$item->qty.'</i></div>';
        } else if($item->type == 'no inventory') {
            $max_quantity = 999;
        }
        
        if(!$order) {
            $modal .= '<input id="quantity_input" type="text" value="1" name="quantity_input">';
            $modal .= '<div id="quantity-danger">Kuantiti Melebihi Inventori</div>';
        } else {
            $modal .= '<input id="quantity_input" type="text" value="'.$order->qty.'" name="quantity_input">';
            $modal .= '<div id="quantity-danger">Kuantiti Melebihi Inventori</div>';
            $modal .= '<div class="row justify-content-center"><i>Dalam Troli : '.$order->qty * $order->unit_qty.' Unit</i></div>';
        }

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $max_quantity]);
    }

    public function countItemsInCart(Request $request)
    {
        $order = null;
        $count_order = 0;
        $order = DB::table('pgng_orders')->where('user_id', Auth::id())->where('organization_id', $request->org_id)->where('status', 'In cart')->select('id')->first();
        if($order) {
            $count_order = DB::table('product_order')->where('pgng_order_id', $order->id)->count();
        }

        return response()->json(['counter' => $count_order]);
    }

    public function storeItemInCart(Request $request)
    {
        $msg = '';
        $user_id = Auth::id();
        $new_total_price = 0;

        if($request->qty == null) {
            $msg = "Sila masukkan nilai.";
            return response()->json(['alert' => $msg]);
        }
        
        $item = ProductItem::where('id', $request->i_id)
        ->select('type', 'quantity_available as qty', 'selling_quantity as unit_qty', 'price')
        ->first();

        $order = DB::table('pgng_orders')
        ->where([
            ['user_id', $user_id],
            ['status', 'In cart'],
            ['organization_id', $request->o_id],
            ['deleted_at',NULL]
        ])->select('id')->first();
            
        // Check if quantity request is less or equal to quantity available
        if($item->type == 'have inventory' && ($request->qty * $item->unit_qty) > $item->qty && !$order) {
            $msg = "Stock Barang Ini Tidak Mencukupi | Stock : ".$item->qty;
            return response()->json(['alert' => $msg]);
        }
        
        // Check if order already exists
        if($order) // order exists
        {
            $cart_exist = ProductOrder::where([
                ['product_item_id', $request->i_id],
                ['pgng_order_id', $order->id],
            ])->select('id', 'quantity as qty', 'selling_quantity as unit_qty')->first();

            // If same item exists in cart
            if($cart_exist) // if exists (update)
            {
                if($item->type == 'have inventory') {
                    $user_quantity = $request->qty * $item->unit_qty;
                    $cart_quantity = $cart_exist->qty * $cart_exist->unit_qty;
                    $new_stock_qty = $this->calculateNewQuantity($user_quantity, $item->qty, $cart_quantity);
                    if($new_stock_qty < 0) {
                        $msg = "Stock Barang Ini Tidak Mencukupi | Stock : ".$item->qty;
                        return response()->json(['alert' => $msg]);
                    }
                }

                ProductOrder::where('id', $cart_exist->id)->update([
                    'quantity' => $request->qty,
                    'selling_quantity' => $item->unit_qty
                ]);
            }
            else // if not exists (insert)
            {
                if($item->type == 'have inventory') {
                    $new_stock_qty = intval((int)$item->qty - (int)($request->qty * $item->unit_qty));
                    if($new_stock_qty < 0) {
                        $msg = "Stock Barang Ini Tidak Mencukupi | Stock : ".$item->qty;
                        return response()->json(['alert' => $msg]);
                    }
                }
                
                ProductOrder::create([
                    'quantity' => $request->qty,
                    'selling_quantity' => $item->unit_qty,
                    'product_item_id' => $request->i_id,
                    'pgng_order_id' => $order->id
                ]);
            }

            $new_total_price = $this->calculateTotalPrice($order->id);

            DB::table('pgng_orders')->where('id', $order->id)->update([
                'total_price' => $new_total_price
            ]);
            
        }
        else // order did not exists
        {
            $fixed_charges = $this->getFixedCharges($request->o_id);
            $total_price = ($item->price * (int)($request->qty * $item->unit_qty)) + $fixed_charges;

            if($item->type == 'have inventory') {
                $new_stock_qty = intval((int)$item->qty - (int)($request->qty * $item->unit_qty));
            }
            
            $new_order_id = DB::table('pgng_orders')->insertGetId([
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'total_price' => $total_price,
                'status' => 'In cart',
                'user_id' => $user_id,
                'organization_id' => $request->o_id
            ]);

            ProductOrder::create([
                'quantity' => $request->qty,
                'selling_quantity' => $item->unit_qty,
                'product_item_id' => $request->i_id,
                'pgng_order_id' => $new_order_id
            ]);
        }
        //no need to update product item quantity when the item just in cart
        // if($item->type == 'have inventory') {
        //     $msg = $this->updateQuantityAvailable($request->i_id, $new_stock_qty);
        // }
        
        return response()->json(['success' => 'Item Berjaya Direkodkan', 'alert' => $msg]);
    }

    public function showCart($org_id)
    {
        $pickup_date = null;
        $pickup_time = null;
        $fixed_charges = null;
        $user_id = Auth::id();

        $cart = DB::table('pgng_orders')->where([
            ['status', 'In cart'],
            ['organization_id', $org_id],
            ['user_id', $user_id],
        ])->select('id', 'order_type', 'pickup_date', 'total_price', 'note', 'organization_id as org_id')->first();
        
        if($cart) {
            $pickup_date = $cart->pickup_date != null ? Carbon::parse($cart->pickup_date)->format('m/d/Y') : '';
            $pickup_time = $cart->pickup_date != null ? Carbon::parse($cart->pickup_date)->format('H:i') : ''; 
            
            $fixed_charges = $this->getFixedCharges($cart->org_id);
        }

        $response = (object)[
            'org_id' => $org_id,
            'pickup_date' => $pickup_date,
            'pickup_time' => $pickup_time,
            'fixed_charges' => $fixed_charges,
        ];

        return view('merchant.regular.cart', compact('response', 'cart'));
    }

    public function getAllItemsInCart(Request $request)
    {
        $c_id = $request->id;
        
        $cart_item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->where('po.pgng_order_id', $c_id)
                ->select('po.id', 'pi.name', 'po.quantity', 'po.selling_quantity', 'pi.price')
                ->get();

        if(request()->ajax()) 
        { 
            $table = Datatables::of($cart_item);

            $table->editColumn('full_quantity', function ($row) {
                return $row->quantity * $row->selling_quantity;
            });

            $table->editColumn('price', function ($row) {
                return number_format((double)(($row->price * $row->selling_quantity)), 2);
            });

            $table->editColumn('action', function ($row) {
                $type = request()->get('type');
                if($type == 'cart'){
                    $label = '<button type="button" data-cart-order-id="'.$row->id.'" class="delete-item btn btn-danger"><i class="fas fa-trash-alt"></i></button>';
                } else {
                    $label = number_format((double)(($row->price * $row->selling_quantity) * $row->quantity), 2);
                }
                
                return $label;
            });

            $table->rawColumns(['full_quantity', 'price', 'action']);

            return $table->make(true);
        }

    }

    public function destroyItemInCart(Request $request)
    {
        $cart_id = $request->cart_id;
        
        $cart_item = ProductOrder::where('id', $cart_id)
        ->select('quantity as qty', 'selling_quantity as unit_qty', 'product_item_id as i_id', 'pgng_order_id as o_id')
        ->first();
        $product_item = ProductItem::where('id', $cart_item->i_id)
        ->select('id', 'type', 'quantity_available as qty')
        ->first();
        
        // if($product_item->type == 'have inventory') {
        //     $new_quantity = intval($product_item->qty + ($cart_item->qty * $cart_item->unit_qty)); // increment quantity
        //     /* If previous product item is being unavailable because of added item in cart,
        //     after the item deleted, the quantity in product_item will increment back and
        //     the item will be available */
        //     if($product_item->qty == 0)
        //     {
        //         ProductItem::where('id', $product_item->id)->update([
        //             'quantity_available' => $new_quantity,
        //             'status' => 1,
        //         ]);
        //     }
        //     else
        //     {
        //         ProductItem::where('id', $product_item->id)->update([
        //             'quantity_available' => $new_quantity,
        //         ]);
        //     }
        // }
        
        //ProductOrder::where('id', $cart_id)->forceDelete();
        ProductOrder::where('id', $cart_id)->update([
            'deleted_at' => Carbon::now()
        ]);
        
        $total_price = $this->calculateTotalPrice($cart_item->o_id);

        if($total_price != null) {
            DB::table('pgng_orders')->where('id', $cart_item->o_id)->update([
                'updated_at' => Carbon::now(),
                'total_price' => $total_price
            ]);
        } else {
            //DB::table('pgng_orders')->where('id', $cart_item->o_id)->delete();
            DB::table('pgng_orders')->where('id', $cart_item->o_id)->update([
                'deleted_at' => Carbon::now()
            ]);
        }
    }

    public function fetchDisabledDates(Request $request) 
    {
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addMonths(2);
        $dates = array();
        $period = CarbonPeriod::create($start_date, $end_date);

        $org_day = OrganizationHours::where('organization_id', $request->org_id)
        ->where('status', 0)
        ->select('day')->get();

        foreach($period as $row) {
            $day_name = $row->format('l');
            $day_int = RegularMerchantController::getDayIntegerByDayName($day_name);
            foreach($org_day as $org) {
                if($day_int == $org->day) {
                    $dates[] = $row->format('m/d/Y');
                }
            }
        }
        
        return response()->json(['dates' => $dates, 'org_id' => $org_day]);
    }

    public function fetchOperationHours(Request $request)
    {
        $date = Carbon::parse($request->date);
        $day_int = RegularMerchantController::getDayIntegerByDayName($date->format('l'));

        $op_hour = OrganizationHours::where('organization_id', $request->org_id)
        ->where('day', $day_int)
        ->select('open_hour', 'close_hour')
        ->first();

        $open_hour = Carbon::parse($op_hour->open_hour)->format('g:i A');
        $close_hour = Carbon::parse($op_hour->close_hour)->format('g:i A');

        $open_hour_f = Carbon::parse($op_hour->open_hour)->format('G:i');
        $close_hour_f = Carbon::parse($op_hour->close_hour)->format('G:i');

        $isToday = RegularMerchantController::compareDateWithToday($date);

        $current_time = Carbon::now()->format('G:i');
        // If current time is greater than closing time of organization and it is today dates then it will close
        if($current_time > $close_hour_f && $isToday) { // 12 > 11
            $isOpen = false;
            $body = '<p>Tutup pada masa ini</p>';
        } else {
            $isOpen = true;
            if($isToday) {
                $temp_open_hour = Carbon::parse($op_hour->open_hour)->format('G:i');
                // If current time is greater than opening time of the organization then the user will be able to choose current time until closing time
                if($current_time >= $temp_open_hour) { // 00 >= 8
                    $open_hour_f = $current_time;
                    $body = '<p>Waktu Buka dari Sekarang - '.$close_hour.'</p>';
                } else {
                    $body = '<p>Waktu Buka dari '.$open_hour.' - '.$close_hour.'</p>';
                }
            } else {
                $body = '<p>Waktu Buka dari '.$open_hour.' - '.$close_hour.'</p>';
            }
        }
        
        $response = array(
            "open" => $isOpen,
            "min" => $open_hour_f,
            "max" => $close_hour_f,
            'body' => $body
        );

        return response()->json(['hour' => $response]);
    }

    public function store(Request $request, $org_id, $order_id)
    {
        $pickup_date = $request->pickup_date;
        $pickup_time = $request->pickup_time;
        $note = $request->note;
        $order_type = $request->order_type;
        
        if($this->validateRequestedPickupDate($pickup_date, $pickup_time, $org_id) == false) {
            return back()->with('error', 'Sila pilih masa yang sesuai');
        }
        
        if($order_type == 'Pick-Up') {
            $pickup_datetime = Carbon::parse($pickup_date)->format('Y-m-d').' '.Carbon::parse($pickup_time)->format('H:i:s');

            DB::table('pgng_orders')->where('id', $order_id)->update([
                'updated_at' => Carbon::now(),
                'order_type' => $order_type,
                'pickup_date' => $pickup_datetime,
                'note' => $note,
            ]);
        }
        
        $cart = DB::table('pgng_orders')
        ->where('id', $order_id)->select('id', 'pickup_date', 'note', 'total_price')->first();

        $pickup_date_f = Carbon::parse($cart->pickup_date)->format('d-m-y h:i A');

        $response = (object)[
            'note' => $cart->note,
            'pickup_date' => $pickup_date_f,
            'amount' => number_format((double)$cart->total_price, 2),
        ];
            
        return view('merchant.regular.pay', compact('cart', 'response'));
        
    }

    private function validateRequestedPickupDate($pickup_date, $pickup_time, $org_id)
    {
        $isAvailable = true;

        $isToday = RegularMerchantController::compareDateWithToday($pickup_date);
        $day = RegularMerchantController::getDayIntegerByDayName(Carbon::parse($pickup_date)->format('l'));

        $hour = OrganizationHours::where('organization_id', $org_id)->where('day', $day)->first();

        if($isToday) {
            $current_time = Carbon::now()->format('G:i');
            // If the pickup time is less than the current time then return error
            if(Carbon::parse($pickup_time)->lt($current_time)) { // 11 < 12
                $isAvailable = false;
            }
        }

        if($hour->status == 0)
        {
            $isAvailable = false;
        } else {
            $pickup_time = Carbon::parse($pickup_time)->format('H:i:s');
            // 10:00 <= 10:01 AND 22:00 >= 10:01
            if(($hour->open_hour <= $pickup_time && $hour->close_hour >= $pickup_time) == false) {
                $isAvailable = false;
            }
        }

        return $isAvailable;
    }

    public static function getFixedCharges($org_id)
    {
        $fixed_charges = Organization::find($org_id)->fixed_charges;
        $fixed_charges = $fixed_charges != null ? $fixed_charges : 0;

        return $fixed_charges;
    }

    private function calculateNewQuantity($user_qty, $qty_available, $cart_qty)
    {
        $new_stock_qty = null;

        if($user_qty > $cart_qty) // request qty more than existing qty
        {
            $new_stock_qty = intval($qty_available - ($user_qty - $cart_qty)); // decrement stock
        }
        else if($user_qty < $cart_qty) // request qty less than existing qty
        {
            $new_stock_qty = intval($qty_available + ($cart_qty - $user_qty)); // increment stock
        }
        else if($user_qty == $cart_qty) // request qty equal existing qty
        {
            $new_stock_qty = intval((int)$qty_available - 0); // stock not change
        }

        return $new_stock_qty;
    }

    private function updateQuantityAvailable($item_id, $new_stock_qty)
    {
        // check if quantity is 0 after add to cart
        if($new_stock_qty != 0) // if not 0
        {
            ProductItem::where('id', $item_id)->update(['quantity_available' => $new_stock_qty]);
            $msg = '';
        }
        else // if 0 (change item status)
        {
            ProductItem::where('id', $item_id)
            ->update(['quantity_available' => $new_stock_qty, 'status' => 0]);
            $msg = 'restart';
        }

        return $msg;
    }

    private function calculateTotalPrice($order_id) 
    {
        $new_total_price = null;

        $cart_item = DB::table('product_order as po')
            ->join('product_item as pi', 'po.product_item_id', 'pi.id')
            ->where([
                ['po.pgng_order_id' , $order_id],
                ['po.deleted_at', NULL],
                ['pi.deleted_at', NULL]
            ])
            ->select('po.quantity as qty', 'pi.price', 'pi.selling_quantity as unit_qty')
            ->get();
        
        $order = PgngOrder::find($order_id);
        
        if ($order) {
            $org_id = $order->organization_id;
            $fixed_charges = $this->getFixedCharges($org_id);
            
            if (count($cart_item) != 0) {
                foreach ($cart_item as $row) {
                    $new_total_price += doubleval($row->price * ($row->qty * $row->unit_qty));
                }
                $new_total_price += $fixed_charges;
            }
        }
        
        return $new_total_price;
    }
}
