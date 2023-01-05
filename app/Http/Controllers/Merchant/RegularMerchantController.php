<?php

namespace App\Http\Controllers\Merchant;

use App\User;
use App\Http\Controllers\Controller;
use App\Mail\MerchantOrderReceipt;
use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\PgngOrder;
use App\Models\Transaction;
use App\Models\ProductItem;
use App\Models\ProductOrder;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\FromView;
use Yajra\DataTables\DataTables;

class RegularMerchantController extends Controller
{
    public function index()
    {
        $todayDate = Carbon::now()->format('l'); // Format to day name
        
        $day = $this->getDayIntegerByDayName($todayDate); // Convert to integer
        $type_org_id = DB::table('type_organizations')->where('nama', 'Peniaga Barang Umum')->first()->id;

        $merchant = Organization::
        join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
        ->where([
            ['deleted_at', null],
            ['type_org', $type_org_id],
            ['day', $day]
        ])
        ->select('organizations.id as id', 'nama', 'address', 'postcode', 'state', 'city', 'organization_picture',
        'day', 'open_hour', 'close_hour', 'status')
        ->orderBy('status', 'desc')
        ->paginate(3);
        
        // dd($merchant);
        
        // foreach($merchant as $row)
        // {
        //     $oh_status[$row->id] = $row->organization_hours->first()->status;
        // }

        return view('merchant.regular.index', compact('merchant'));
    }

    public function test_index(Request $request)
    {
        $merchant_arr = array();
        if($request->ajax()) {
            $todayDate = Carbon::now()->format('l'); // Format to day name
        
            $day = $this->getDayIntegerByDayName($todayDate); // Convert to integer
            $type_org_id = DB::table('type_organizations')->where('nama', 'Peniaga Barang Umum')->first()->id;

            $merchant = Organization::
            join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
            ->where([
                ['deleted_at', null],
                ['type_org', $type_org_id],
                ['day', $day]
            ])
            ->select('organizations.id as id', 'nama', 'address', 'postcode', 'state', 'city', 'organization_picture as picture',
            'day', 'open_hour', 'close_hour', 'status')
            ->orderBy('status', 'desc')
            ->get();

            $count = 0;

            foreach($merchant as $row)
            {
                $nama = $row->nama;
                $picture = "images/koperasi/default-item.png";
                if($row->picture != null){
                    $picture = "organization_picture/".$row->picture;
                }
                if($row->status == 0) {
                    $nama = $row->nama." <label class='text-danger'>Closed</label>";
                }
                $merchant_arr[] = array(
                    "id" => $row->id,
                    "nama" => $nama,
                    "address" => $row->address,
                    "postcode" => $row->postcode,
                    "state" => $row->state,
                    "city" => $row->city,
                    "picture" => $picture,
                    "day" => $row->day,
                    "open_hour" => $row->open_hour,
                    "close_hour" => $row->status,
                );

                $count++;
            }

            return response()->json(['merchant' => $merchant_arr, 'count' => $count]);
        }
    }

    public function show($id)
    {
        $todayDate = Carbon::now()->format('l');
        
        $day = $this->getDayIntegerByDayName($todayDate);

        $merchant = Organization::
        join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
        ->where([
            ['day', $day],
            ['organizations.id', $id]
        ])
        ->select('organizations.id as id', 'nama', 'address', 'postcode', 'state', 'city', 'fixed_charges',
        'day', 'open_hour', 'close_hour', 'status')
        ->first();

        $fixed_charges = $merchant->fixed_charges != null ? $merchant->fixed_charges : 0;

        $product_group = DB::table('product_group as pg')
        ->join('product_item as pi', 'pi.product_group_id', 'pg.id')
        ->where([
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
            $price[$row->id] = number_format((double)(($row->price * $row->selling_quantity) + $fixed_charges), 2, '.', '');
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
        } else {
            $modal .= '<input id="quantity_input" type="text" value="'.$order->qty.'" name="quantity_input">';
            $modal .= '<div class="row justify-content-center"><i>Dalam Troli : '.$order->qty.' X '.$order->unit_qty.' Unit</i></div>';
        }

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $max_quantity]);
    }

    public function storeItemInCart(Request $request)
    {
        $msg = '';
        $user_id = Auth::id();
        $new_total_price = 0;
        
        $item = ProductItem::where('id', $request->i_id)
        ->select('type', 'quantity_available as qty', 'selling_quantity as unit_qty', 'price')
        ->first();

        $order = DB::table('pgng_orders')
        ->where([
            ['user_id', $user_id],
            ['status', 'In cart'],
            ['organization_id', $request->o_id]
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

        if($item->type == 'have inventory') {
            $msg = $this->updateQuantityAvailable($request->i_id, $new_stock_qty);
        }
        
        return response()->json(['success' => 'Item Berjaya Direkodkan', 'alert' => $msg]);
    }

    public function showCart($org_id)
    {
        $price = array();
        $cart_item = array(); // empty if cart is empty
        $user_id = Auth::id();

        $cart = DB::table('pgng_orders')->where([
            ['status', 'In cart'],
            ['organization_id', $org_id],
            ['user_id', $user_id],
        ])->select('id', 'total_price', 'organization_id as org_id')->first();
        
        if($cart) {
            $cart_item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                ->where('po.pgng_order_id', $cart->id)
                ->select('po.id', 'po.quantity', 'po.selling_quantity', 'pi.name', 'pi.price')
                ->get();

            $fixed_charges = $this->getFixedCharges($org_id);

            foreach($cart_item as $row)
            {
                $price[$row->id] = number_format((double)(($row->price * $row->selling_quantity) + $fixed_charges), 2, '.', '');
            }
        }

        return view('merchant.regular.cart', compact('cart', 'cart_item', 'price'));
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
        
        if($product_item->type == 'have inventory') {
            $new_quantity = intval($product_item->qty + ($cart_item->qty * $cart_item->unit_qty)); // increment quantity
            /* If previous product item is being unavailable because of added item in cart,
            after the item deleted, the quantity in product_item will increment back and
            the item will be available */
            if($product_item->qty == 0)
            {
                ProductItem::where('id', $product_item->id)->update([
                    'quantity_available' => $new_quantity,
                    'status' => 1,
                ]);
            }
            else
            {
                ProductItem::where('id', $product_item->id)->update([
                    'quantity_available' => $new_quantity,
                ]);
            }
        }
        
        ProductOrder::where('id', $cart_id)->forceDelete();
        
        $total_price = $this->calculateTotalPrice($cart_item->o_id);

        if($total_price != null) {
            DB::table('pgng_orders')->where('id', $cart_item->o_id)->update([
                'updated_at' => Carbon::now(),
                'total_price' => $total_price
            ]);
        } else {
            DB::table('pgng_orders')->where('id', $cart_item->o_id)->delete();
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
            $day_int = $this->getDayIntegerByDayName($day_name);
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
        $day_int = $this->getDayIntegerByDayName($date->format('l'));

        $op_hour = OrganizationHours::where('organization_id', $request->org_id)
        ->where('day', $day_int)
        ->select('open_hour', 'close_hour')
        ->first();

        $open_hour = Carbon::parse($op_hour->open_hour)->format('g:i A');
        $close_hour = Carbon::parse($op_hour->close_hour)->format('g:i A');

        $open_hour_f = Carbon::parse($op_hour->open_hour)->format('G:i');
        $close_hour_f = Carbon::parse($op_hour->close_hour)->format('G:i');

        $isToday = $this->compareDateWithToday($date);

        $current_time = Carbon::now()->format('G:i');
        if($current_time > $close_hour_f) { // 12 > 11
            $isOpen = false;
            $body = '<p>Tutup pada masa ini</p>';
        } else {
            $isOpen = true;
            if($isToday) {
                $temp_open_hour = Carbon::parse($op_hour->open_hour)->format('G:i');
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

    public function storeOrder(Request $request)
    {
        $pickup_date = $request->pickup_date;
        $pickup_time = $request->pickup_time;
        $note = $request->note;
        $o_id = $request->order_id;
        $order_type = $request->order_type;

        $isToday = $this->compareDateWithToday($pickup_date);

        if($isToday) {
            $current_time = Carbon::now()->format('G:i');
            if(Carbon::parse($pickup_time)->format('G:i') < $current_time) { // 11 < 12
                return back()->with('error', 'Sila pilih masa yang sesuai');
            }
        }
        
        if($order_type == 'Pick-Up') {
            $pickup_datetime = Carbon::parse($pickup_date)->format('Y-m-d').' '.Carbon::parse($pickup_time)->format('h:i:s');

            DB::table('pgng_orders')->where('id', $o_id)->update([
                'updated_at' => Carbon::now(),
                'order_type' => $order_type,
                'pickup_date' => $pickup_datetime,
                'note' => $note,
                'status' => 'Paid'
            ]);
        }

        $order = PgngOrder::where('id', $o_id)->first();
        $organization = Organization::find($order->organization_id);
        $transaction = Transaction::find(7);
        $user = User::find(Auth::id());

        Mail::to($user->email)->send(new MerchantOrderReceipt($order, $organization, $transaction, $user));
        
        return redirect('/merchant/regular/')->with('success', 'Pesanan Anda Berjaya Direkod!');
    }

    public function showAllOrder()
    {
        return view('merchant.order');
    }

    public function getAllOrder(Request $request)
    {
        $total_price[] = 0;
        $pickup_date[] = 0;
        $status = ['Paid'];
        
        $order = $this->getAllOrderQuery($status);
        
        if(request()->ajax()) 
        {
            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == 'Paid') {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>';
                    return $btn;
                } else {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>';
                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $btn = '<div class="d-flex justify-content-center align-items-center">';
                $btn = $btn.'<button type="button" class="btn-cancel-order btn btn-danger" data-order-id="'.$row->id.'">';
                $btn = $btn.'<i class="fas fa-trash-alt"></i></button></div>';

                return $btn;
            });

            $table->editColumn('note', function ($row) {
                if($row->note != null) {
                    return $row->note;
                } else {
                    return "<i>Tiada Nota</i>";
                }
            });

            $table->editColumn('total_price', function ($row) {
                $total_price = number_format($row->total_price, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('merchant.order-detail', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
            });

            $table->rawColumns(['note', 'total_price', 'status', 'action']);

            return $table->make(true);
        }
    }

    public function deletePaidOrder(Request $request)
    {
        $id = $request->o_id;

        $delete_order = DB::table('pgng_orders')->where('id', $id)->update([
            'status' => 'Cancel by user',
            'deleted_at' => Carbon::now(),
        ]);
        
        if($delete_order) {
            Session::flash('success', 'Pesanan Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    public function showOrderHistory()
    {
        return view('merchant.history');
    }

    public function getOrderHistory(Request $request)
    {
        $total_price[] = 0;
        $pickup_date[] = 0;
        $status = ['Cancel by user', 'Cancel by merchant', 'Delivered', 'Picked-Up'];
        
        $order = $this->getAllOrderQuery($status);
        
        if(request()->ajax()) 
        {
            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == 'Picked-Up') {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya diambil</span>';
                    return $btn;
                } else {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>';
                    return $btn;
                }
            });

            $table->editColumn('note', function ($row) {
                if($row->note != null) {
                    return $row->note;
                } else {
                    return "<i>Tiada Nota</i>";
                }
            });

            $table->editColumn('total_price', function ($row) {
                $total_price = number_format($row->total_price, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('merchant.order-detail', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
            });

            $table->rawColumns(['note', 'total_price', 'status']);

            return $table->make(true);
        }
    }

    public function showOrderDetail($order_id)
    {
        // Get Information about the order
        $list = DB::table('pgng_orders as pu')
                ->join('organizations as o', 'o.id', 'pu.organization_id')
                ->where('pu.id', $order_id)
                ->select('pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                        'o.nama', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state')
                ->first();

        $order_date = Carbon::parse($list->updated_at)->format('d/m/y H:i A');
        $pickup_date = Carbon::parse($list->pickup_date)->format('d/m/y H:i A');
        $total_order_price = number_format($list->total_price, 2, '.', '');

        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->where('po.pgng_order_id', $order_id)
                ->select('po.id', 'pi.name', 'pi.price', 'po.quantity', 'po.selling_quantity')
                ->get();

        $total_price[] = array();
        $price[] = array();
        
        foreach($item as $row)
        {
            $price[$row->id] = number_format($row->price, 2, '.', '');
            $total_price[$row->id] = number_format(doubleval($row->price * ($row->quantity * $row->selling_quantity)), 2, '.', ''); // calculate total for each item in cart
        }

        return view('merchant.list', compact('list', 'order_date', 'pickup_date', 'total_order_price', 'item', 'price', 'total_price'));
    }

    // public function test_mail()
    // {
        

    //     return new MerchantOrderReceipt($order, $organization, $transaction, $user);
    // }

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
                    ->where('po.pgng_order_id', $order_id)
                    ->select('po.quantity as qty', 'pi.price', 'pi.selling_quantity as unit_qty')
                    ->get();

        $org_id = PgngOrder::where('id', $order_id)->select('organization_id as org_id')->first()->org_id;
        $fixed_charges = $this->getFixedCharges($org_id);
            
        if(count($cart_item) != 0) {
            foreach($cart_item as $row)
            {
                $new_total_price += doubleval($row->price * ($row->qty * $row->unit_qty));
            }
        }

        return $new_total_price + (count($cart_item) * $fixed_charges);
    }

    private function compareDateWithToday($date)
    {
        $today = Carbon::now();
        $date_f = Carbon::parse($date);
        
        if($today->format('d-m-Y') == $date_f->format('d-m-Y')) {
            return true;
        } else {
            return false;
        }
    }

    private function getAllOrderQuery($status)
    {
        $user_id = Auth::id();

        $order = DB::table('pgng_orders as pu')
                ->join('organizations as o', 'pu.organization_id', 'o.id')
                ->whereIn('status', $status)
                ->where('user_id', $user_id)
                ->select('pu.id', 'pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                'o.nama', 'o.telno')
                ->orderBy('status', 'desc')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->get();

        return $order;
    }

    private function getFixedCharges($org_id)
    {
        $fixed_charges = Organization::find($org_id)->fixed_charges;
        $fixed_charges = $fixed_charges != null ? $fixed_charges : 0;

        return $fixed_charges;
    }

    private function getDayIntegerByDayName($date)
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
