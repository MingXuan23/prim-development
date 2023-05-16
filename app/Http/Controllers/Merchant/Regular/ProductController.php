<?php

namespace App\Http\Controllers\Merchant\Regular;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Models\ProductItem;
use App\Models\ProductOrder;
use App\Models\PgngOrder;
use Yajra\DataTables\DataTables;//for datatable

class ProductController extends Controller
{
    //product home page
    public function index(){
        //get products of merchants (Peniaga Barang Umum)
       $products = ProductItem::
       join('product_group as pg','pg.id','product_group_id')
       ->join('organizations as org','org.id','pg.organization_id')
       ->where([
            ['pg.deleted_at',NULL],
            ['product_item.deleted_at',NULL],
            ['product_item.status', 1],
            ['product_item.quantity_available' ,'>', 0],//only get those products that haven't sold out yet
       ])
       ->where([
            ['org.deleted_at',NULL],
            ['org.type_org',9]
       ])
       ->select('product_item.name','product_item.id','price','image','org.code')
       ->inRandomOrder()//randomize the row
       //->get();//get() to get multiple rows and put in into a collection
       ->paginate(12);
       foreach($products as $product){
            $product->price = number_format($product->price,2);
       }
        return view('merchant.regular.product.index',compact('products'));
    }
    public function show($id){
          //get details of the product clicked (including merchant's details and product group)
          $product = ProductItem::
          join('product_group as pg','pg.id','product_group_id')
          ->join('organizations as org','org.id','pg.organization_id')
          ->where([
               ['pg.deleted_at',NULL],
               ['product_item.deleted_at',NULL],
               ['product_item.id',$id]
          ])
          ->where('org.deleted_at',NULL)
          ->select('product_item.name','product_item.id','price','image','desc','quantity_available','collective_noun','product_group_id',DB::raw('pg.name as pg_name'),'pg.organization_id',DB::raw('org.nama as org_name'),'district','city','organization_picture','code')//need to use DB::raw because both table have same column name
          ->first(); //first() to get a single row
          $product->price = number_format($product->price,2);
          return view('merchant.regular.product.show',compact('product'));
    }
    public function showAllCart(){
          $user_id = Auth::id();
          $productInCart = DB::table('product_order')
          ->join('pgng_orders as po','po.id','pgng_order_id')
          ->where([
               ['po.user_id',$user_id],
               ['po.status','In cart'],
               ['po.deleted_at',NULL],
               ['product_order.deleted_at',NULL]
          ])
          ->join('product_item as pi','pi.id','product_item_id')
          ->where([
               ['pi.deleted_at',NULL]
          ])
          ->join('organizations as org','org.id','po.organization_id')
          ->where([
               ['org.deleted_at',NULL]
          ])
          ->select('product_order.id','quantity','product_item_id','pgng_order_id',
          'pi.name','quantity_available','price','collective_noun','pi.status','pi.image',
          'org.nama','organization_picture','code',
          'po.total_price')
          ->orderBy('product_order.id','desc')
          ->get();
          foreach($productInCart as $product){
               $product->price = number_format($product->price,2);
               $product->total_price = number_format($product->total_price,2);

          }
          $organizations = DB::table('pgng_orders as po')
          ->join('organizations as org','org.id','organization_id')
          ->where([
               ['org.deleted_at',NULL],
               ['po.deleted_at',NULL],
               ['po.user_id',$user_id],
               ['po.status','In cart']
          ])
          ->select('org.id','nama')
          ->distinct('nama')
          ->orderBy('po.id','desc')
          ->get();

          return view('merchant.regular.product.productsCart',compact('productInCart','organizations'));
    } 
    public function calculateTotalPrice($order_id,$charge) 
    {
        $new_total_price = null;

        $cart_item = DB::table('product_order as po')
                    ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                    ->where([
                         ['po.pgng_order_id', $order_id],
                         ['pi.quantity_available','>',0],
                         ['pi.type','have inventory'],
                         ['pi.status',1],
                         ['pi.deleted_at',NULL],
                         ['po.deleted_at',NULL]
                    ])
                    ->select('po.quantity as qty', 'pi.price')
                    ->get();

        $fixed_charges = $charge;
     
        if(count($cart_item) != 0) {
            foreach($cart_item as $row)
            {
                    $new_total_price += doubleval($row->price * $row->qty );
            }          
        }
        $new_total_price += $fixed_charges;
         return $new_total_price;
    }
        
       
    
    //for updating a cart 
    public function updateCart(Request $request){
     // to update quantity in ProductOrder
         ProductOrder::find($request->productOrderId)
         ->update([
               'quantity'=> $request->qty,
               'updated_at' => Carbon::now(),
         ]);
     //find the price for a single quantity
         $productItemId = ProductOrder::find($request->productOrderId)
         ->product_item_id;

         $price = ProductItem::find($productItemId)
         ->price;
     //find the service charge rate
         $organizationId = DB::table('pgng_orders')
         ->find($request->pgngOrderId)
         ->organization_id;

         $charge = DB::table('organizations')
         ->find($organizationId)
         ->fixed_charges;
     // to update total price in PGNGOrder
         $totalPrice = $this->calculateTotalPrice($request->pgngOrderId, $charge);
         DB::table('pgng_orders')
         ->where('id', $request->pgngOrderId)
         ->update([
               'total_price' => $totalPrice,
               'updated_at' => Carbon::now(),
         ]);
         $totalPrice = number_format($totalPrice, 2);
          return response()->json(['success' => 'Item Berjaya Direkodkan', 'totalPrice' => $totalPrice]);
    }
    //to get the number of items in cart
   public function loadCartCounter(){
          $user_id = Auth::id();
          $cartItemsCounter = DB::table('pgng_orders')
          ->join('product_order as po','po.pgng_order_id','pgng_orders.id')
          ->join('product_item as pi','pi.id','po.product_item_id')
          ->where([
               ['user_id', $user_id],
               ['pgng_orders.status','In cart'],
               ['pi.quantity_available','>',0],
               ['po.deleted_at',NULL],
               ['pi.deleted_at',NULL],
               ['pgng_orders.deleted_at',NULL]
          ])
          ->count();
          
          //to get the total price of all valid order
          $subquery = DB::table('product_order')
          ->join('product_item', 'product_item.id', '=', 'product_order.product_item_id')
          ->where([
               ['product_item.quantity_available', '>', 0],
               ['product_item.deleted_at',NULL],
               ['product_order.deleted_at',NULL]
          ])
          ->join('pgng_orders', 'pgng_orders.id', '=', 'product_order.pgng_order_id')
          ->where([
               ['pgng_orders.status', 'In cart'],
               ['pgng_orders.deleted_at',NULL]
          ])
          ->distinct('pgng_orders.id')
          ->select('pgng_orders.id', 'total_price');

          $cartTotalPrice = DB::table('pgng_orders')
          ->joinSub($subquery, 'sub', function ($join) {
               $join->on('pgng_orders.id', '=', 'sub.id');
          })
          ->where('user_id', $user_id)
          ->where('status', 'In cart')
          ->selectRaw('SUM(sub.total_price) as cart_total_price')
          ->value('cart_total_price');

          $cartTotalPrice = number_format($cartTotalPrice, 2);

          return response()->json(['counter'=>$cartItemsCounter,'total'=>$cartTotalPrice]);
   }
   public function getTotalPrice(Request $request ){
     $pgng_id = $request->pgng_order_id;
     $charge = Organization::find($request->org_id)
     ->fixed_charges;
     $totalPrice = $this->calculateTotalPrice($pgng_id, $charge);
     // update total in PGNG_ORDERS
     DB::table('pgng_orders')
         ->where([
          ['id', $pgng_id]
          ])
         ->update([
               'total_price' => $totalPrice
         ]);
     $totalPrice = number_format($totalPrice, 2);
     return response()->json(['totalPrice'=>$totalPrice]);
   }
//    public function checkOut(){
//           $user_id = Auth::id();
//           $products = DB::table('product_order')
//           ->join('pgng_orders as po','po.id','pgng_order_id')
//           ->where([
//                ['po.user_id',$user_id],
//                ['po.status','In cart']
//           ])
//           ->join('product_item as pi','pi.id','product_item_id')
//           ->where([
//                ['pi.deleted_at',NULL],
//                ['pi.quantity_available' ,'>', 0]
//           ])
//           ->join('organizations as org','org.id','po.organization_id')
//           ->where([
//                ['org.deleted_at',NULL]
//           ])
//           ->select('product_order.id','quantity','product_order.selling_quantity','product_item_id','pgng_order_id',
//           'pi.name','price',
//           'org.nama',
//           'po.total_price')
//           ->orderBy('product_order.id','desc')
//           ->get();
//           foreach($products as $product){
//                $product->price = number_format($product->price,2);
//                $product->total_price = number_format($product->total_price,2);
//           }
          
//           $organizations = DB::table('product_order')
//           ->join('pgng_orders as po','po.id','pgng_order_id')
//           ->where([
//                ['po.user_id',$user_id],
//                ['po.status','In cart'],
//                ['po.deleted_at',NULL],
//           ])
//           ->join('product_item as pi','pi.id','product_item_id')
//           ->where([
//                ['pi.deleted_at',NULL],
//                ['pi.quantity_available' ,'>', 0]
//           ])
//           ->join('organizations as org','org.id','po.organization_id')
//           ->where([
//                ['org.deleted_at',NULL]
//           ])
//           ->select('org.id','nama','fixed_charges')
//           ->distinct('nama')
//           ->orderBy('po.id','desc')
//           ->get();
//           //to get total price of valid order    
//           $subquery = DB::table('product_order')
//           ->join('product_item', 'product_item.id', '=', 'product_order.product_item_id')
//           ->where('product_item.quantity_available', '>', 0)
//           ->join('pgng_orders', 'pgng_orders.id', '=', 'product_order.pgng_order_id')
//           ->where('pgng_orders.status', 'In cart')
//           ->distinct('pgng_orders.id')
//           ->select('pgng_orders.id', 'total_price');

//           $cartTotalPrice = DB::table('pgng_orders')
//           ->joinSub($subquery, 'sub', function ($join) {
//                $join->on('pgng_orders.id', '=', 'sub.id');
//           })
//           ->where('user_id', $user_id)
//           ->where('status', 'In cart')
//           ->selectRaw('SUM(sub.total_price) as cart_total_price')
//           ->value('cart_total_price');

//           $cartTotalPrice = number_format($cartTotalPrice, 2);
//           return view('merchant.regular.product.checkout',compact('products','organizations','cartTotalPrice'));
//    }
   public function checkOut($org_id){
        $pickup_date = null;
        $pickup_time = null;
        $fixed_charges = null;
        $user_id = Auth::id();

        $cart = DB::table('pgng_orders')->where([
            ['status', 'In cart'],
            ['organization_id', $org_id],
            ['user_id', $user_id],
            ['deleted_at',NULL]
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

     return view('merchant.regular.product.checkout', compact('response', 'cart'));
   }
   public static function getFixedCharges($org_id)
    {
        $fixed_charges = Organization::find($org_id)->fixed_charges;
        $fixed_charges = $fixed_charges != null ? $fixed_charges : 0;

        return $fixed_charges;
    }
    public function getCheckoutItems(Request $request)
    {
        $c_id = $request->id;
        
        $cart_item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->where([
                    ['po.pgng_order_id', $c_id],
                    ['po.deleted_at',NULL],
                    ['pi.quantity_available','>',0],
                    ['pi.type','have inventory'],
                    ['pi.status',1],
                ])
                ->select('po.id', 'pi.name', 'po.quantity', 'pi.price')
                ->get();

        if(request()->ajax()) 
        { 
            $table = Datatables::of($cart_item);

            $table->editColumn('price', function ($row) {
                return number_format(($row->price), 2);
            });
            $table->editColumn('sub_total', function ($row) {
               return number_format((double)(($row->price * $row->quantity)), 2);
           });
            return $table->make(true);
        }

    }
//    public function store(Request $request, $org_id, $order_id)
//     {
//         $pickup_date = $request->pickup_date;
//         $pickup_time = $request->pickup_time;
//         $note = $request->note;
//         $order_type = $request->order_type;
        
//         if($this->validateRequestedPickupDate($pickup_date, $pickup_time, $org_id) == false) {
//             return back()->with('error', 'Sila pilih masa yang sesuai');
//         }
        
//         if($order_type == 'Pick-Up') {
//             $pickup_datetime = Carbon::parse($pickup_date)->format('Y-m-d').' '.Carbon::parse($pickup_time)->format('H:i:s');

//             DB::table('pgng_orders')->where('id', $order_id)->update([
//                 'updated_at' => Carbon::now(),
//                 'order_type' => $order_type,
//                 'pickup_date' => $pickup_datetime,
//                 'note' => $note,
//             ]);
//         }
        
//         $cart = DB::table('pgng_orders')
//         ->where('id', $order_id)->select('id', 'pickup_date', 'note', 'total_price')->first();

//         $pickup_date_f = Carbon::parse($cart->pickup_date)->format('d-m-y h:i A');

//         $response = (object)[
//             'note' => $cart->note,
//             'pickup_date' => $pickup_date_f,
//             'amount' => number_format((double)$cart->total_price, 2),
//         ];
            
//         return view('merchant.regular.pay', compact('cart', 'response'));
        
//     }
}