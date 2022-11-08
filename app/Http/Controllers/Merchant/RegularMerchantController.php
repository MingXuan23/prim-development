<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationHours;
use App\Models\PickUpOrder;
use App\Models\ProductItem;
use App\Models\ProductGroup;
use App\Models\ProductOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RegularMerchantController extends Controller
{
    public function index()
    {
        $todayDate = Carbon::now()->format('l');
        
        $day = $this->getDayIntegerByDayName($todayDate);

        $merchant = Organization::
        join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
        ->where([
            ['deleted_at', null],
            ['type_org', 3111],
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

    public function show($id)
    {
        $todayDate = Carbon::now()->format('l');
        
        $day = $this->getDayIntegerByDayName($todayDate);

        $merchant = Organization::
        join('organization_hours as oh', 'oh.organization_id', 'organizations.id')
        ->where([
            ['deleted_at', null],
            ['type_org', 3111],
            ['day', $day]
        ])
        ->select('organizations.id as id', 'nama', 'address', 'postcode', 'state', 'city',
        'day', 'open_hour', 'close_hour', 'status')
        ->first();

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
        ->join('product_group as pg', 'pg.id', '=', 'pi.product_group_id')
        ->where([
            ['pg.organization_id', $id],
            ['pg.deleted_at', NULL],
            ['pi.deleted_at', NULL],
        ])
        ->select('pi.id', 'pi.name', 'pi.desc', 'pi.price', 'pi.selling_quantity', 'pi.collective_noun', 'pi.image', 'pi.status', 'pi.product_group_id')
        ->orderBy('pi.product_group_id', 'asc')
        ->orderBy('pi.name')  
        ->get();

        return view('merchant.regular.menu', compact('merchant', 'product_group', 'product_item'));
    }

    public function fetchItem(Request $request)
    {
        $i_id = $request->get('i_id');
        $o_id = $request->get('o_id');
        $modal = '';
        
        $item = ProductItem::where('id', $i_id)
        ->select('id', 'type', 'name', 'price', 'quantity_available', 'selling_quantity')
        ->first();

        if($item->type == 'have inventory') {
            $maxQuantity = ($item->quantity_available / $item->selling_quantity);
            $modal .= '<div class="row justify-content-center"><i>Kuantiti Maximum : '.$item->quantity_available.'</i></div>';
        } else if($item->type == 'no inventory') {
            $maxQuantity = 999;
        }

        $modal .= $this->createQuantityBody($i_id, $o_id, $item->selling_quantity);

        return response()->json(['item' => $item, 'body' => $modal, 'quantity' => $maxQuantity]);
    }

    private function createQuantityBody($item_id, $organization_id, $unit_qty)
    {
        $body = '';
        $user_id = Auth::id();

        $order = DB::table('product_order as po')->join('pgng_orders as pu', 'pu.id', 'po.pgng_order_id')
        ->where([
            ['pu.user_id', $user_id],
            ['pu.organization_id', $organization_id],
            ['po.product_item_id', $item_id],
            ['pu.status', 'In cart'],
        ])
        ->select('quantity')
        ->first();
        
        if(!$order) {
            $body .= '<input id="quantity_input" type="text" value="1" name="quantity_input">';
        } else {
            $body .= '<input id="quantity_input" type="text" value="'.$order->quantity.'" name="quantity_input">';
            $body .= '<div class="row justify-content-center"><i>Dalam Troli : '.$order->quantity.' X '.$unit_qty.' Unit</i></div>';
        }

        return $body;
    }

    public function storeItemInCart(Request $request)
    {
        $msg = '';
        $user_id = Auth::id();
        $new_total_price = 0;
        
        $item = ProductItem::where('id', $request->i_id)
        ->select('type', 'quantity_available as qty', 'selling_quantity as unit_qty', 'price')
        ->first();
        
        // Check if quantity request is less or equal to quantity available
        if($item->type == 'have inventory' && $request->qty > $item->qty) {
            $msg = "Stock Barang Ini Tidak Mencukupi | Stock : ".$item->qty;
            return response()->json(['alert' => $msg]);
        }

        $order = DB::table('pgng_orders')
        ->where([
            ['user_id', $user_id],
            ['status', 'In cart'],
            ['organization_id', $request->o_id]
        ])->select('id')->first();
        
        // Check if order already exists
        if($order) // order exists
        {
            $cart_exist = ProductOrder::where([
                ['product_item_id', $request->i_id],
                ['pgng_order_id', $order->id],
            ])->select('id', 'quantity')->first();

            // If same item exists in cart
            if($cart_exist) // if exists (update)
            {
                if($item->type == 'have inventory') {
                    $user_quantity = $request->qty * $item->unit_qty;
                    $new_stock_qty = $this->calculateNewQuantity($user_quantity, $item->qty, $cart_exist->quantity);
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
                }
                
                ProductOrder::create([
                    'quantity' => $request->qty,
                    'selling_quantity' => $item->unit_qty,
                    'product_item_id' => $request->i_id,
                    'pgng_order_id' => $order->id
                ]);
            }

            $cart_item = DB::table('product_order as po')
                            ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                            ->where('po.pgng_order_id', $order->id)
                            ->select('po.quantity as qty', 'pi.price', 'pi.selling_quantity as unit_qty')
                            ->get();
            
            foreach($cart_item as $row)
            {
                $new_total_price += doubleval($row->price * ($row->qty * $row->unit_qty));
            }

            DB::table('pgng_orders')->where('id', $order->id)->update([
                'total_price' => $new_total_price
            ]);
            
        }
        else // order did not exists
        {
            $total_price = $item->price * (int)($request->qty * $item->unit_qty);

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

    private function calculateNewQuantity($user_qty, $qty_available, $cart_qty)
    {
        $new_stock_qty = null;

        if($user_qty > $cart_qty) // request quant more than existing quant
        {
            $new_stock_qty = intval($qty_available - ($user_qty - $cart_qty)); // decrement stock
        }
        else if($user_qty < $cart_qty) // request quant less than existing quant
        {
            $new_stock_qty = intval($qty_available + ($cart_qty - $user_qty)); // increment stock
        }
        else if($user_qty == $cart_qty) // request quant equal existing quant
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

    public function test_edit()
    {
        return view('merchant.regular.cart');
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
