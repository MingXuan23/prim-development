<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Models\PgngOrder;
use App\Models\ProductOrder;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index()
    {
        $merchant = RegularMerchantController::getAllMerchantOrganization();
        return view('merchant.regular.admin.order.index', compact('merchant'));
    }

    public function getAllOrders(Request $request)
    {
        $org_id = $request->id;
        $total_price[] = 0;
        $pickup_date[] = 0;
        $filter_type = $request->filterType;
        $date = $request->date;

        $order = DB::table('pgng_orders as pu')
                ->join('users as u', 'pu.user_id', 'u.id')
                ->select('pu.id', 'pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                'u.name', 'u.telno')
                ->orderBy('status', 'desc')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->whereIn('status', ["Paid"])
                ->where('organization_id', $org_id);
        
        if(request()->ajax()) 
        {
            if(($filter_type == "" || $filter_type == "all") && $date == "") 
            {
                $order = $order->get();
            }
            else if($filter_type == "date")
            {
                $order = $order->whereBetween('pickup_date', 
                [Carbon::parse($date)->startOfDay()->toDateTimeString(),Carbon::parse($date)->endOfDay()->toDateTimeString()])->get();
            }
            else if($filter_type == "today")
            {
                $order->whereBetween('pickup_date', 
                [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])->get();
            }
            else if($filter_type == "week")
            {
                $order->whereBetween('pickup_date', 
                [Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()])->get();
            }
            else if($filter_type == "month")
            {
                $order->whereBetween('pickup_date', 
                [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfMonth()->toDateTimeString()])->get();
            }else if($filter_type == "receive-today"){
                // to get list of order paid today
                $order = DB::table('pgng_orders as pu')
                ->join('users as u', 'pu.user_id', 'u.id')
                ->select('pu.id', 'pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status',
                'u.name', 'u.telno')
                ->orderBy('status', 'desc')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc')
                ->whereIn('pu.status', ["Paid"])
                ->where('organization_id', $org_id)
                ->join('transactions', 'transactions.id','pu.transaction_id')
                ->where('transactions.status','Success');
                $order->whereBetween('datetime_created', 
                [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])
                ->get();
            }   
            
            $table = Datatables::of($order);
            
            $table->addColumn('status', function ($row) {
                if ($row->status == "Paid") {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>';
                    return $btn;
                } else {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>';
                    return $btn;
                }
            });

            $table->addColumn('action', function ($row) {
                $btn = '<div class="d-flex justify-content-center align-items-center">';
                $btn = $btn.'<button type="button" class="btn-done-pickup btn btn-primary mr-2" data-order-id="'.$row->id.'"><i class="fas fa-clipboard-check"></i></button>';
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
                return number_format($row->total_price, 2, '.', '');
            });

            $table->editColumn('total_price', function ($row) {
                $total_price = number_format($row->total_price, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('admin-reg.order-detail', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y H:i A');
            });

            $table->rawColumns(['note', 'total_price', 'status', 'action']);

            return $table->make(true);
        }
    }

    public function countTotalOrders(Request $request)
    {
        $org_id = $request->id;

        $count_all = PgngOrder::where('organization_id', $org_id)->where('status', 'Paid')->count() ?: 0;
        $count_today = PgngOrder::where('organization_id', $org_id)->where('status', 'Paid')->whereBetween('pickup_date', 
        [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])->count() ?: 0;
        $count_week = PgngOrder::where('organization_id', $org_id)->where('status', 'Paid')->whereBetween('pickup_date', 
        [Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()])->count() ?: 0;
        $count_month = PgngOrder::where('organization_id', $org_id)->where('status', 'Paid')->whereBetween('pickup_date', 
        [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfMonth()->toDateTimeString()])->count() ?: 0;
        $count_receive_today = PgngOrder::where('organization_id', $org_id)->where('pgng_orders.status', 'Paid')
        ->join('transactions','transactions.id','transaction_id')
        ->where('transactions.status','Success')
        ->whereBetween('datetime_created', 
        [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])->count() ?: 0;
        $response = [
            'received_today'=>$count_receive_today,
            'all' => $count_all,
            'today' => $count_today,
            'week' => $count_week,
            'month' => $count_month
        ];

        return response()->json(['response' => $response]);
    }

    public function orderPickedUp(Request $request)
    {
        $update_order = PgngOrder::find($request->o_id)->update([
            'status' => 'Picked-Up',
            'confirm_picked_up_time' => Carbon::now(),
            'confirm_by' => Auth::id(),
        ]);

        if ($update_order) {
            Session::flash('success', 'Pesanan Berjaya Diambil');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Disahkan');
            return View::make('layouts/flash-messages');
        }
    }

    public function showHistory($id)
    {
        $org = Organization::find($id);
        return view('merchant.regular.admin.order.history', compact('org'));
    }

    public function getAllHistories(Request $request)
    {
        $org_id = $request->id;
        $total_price[] = 0;
        $pickup_date[] = 0;
        $filter_type = $request->filterType;
        $date = $request->date;

        $order = DB::table('pgng_orders as pu')
                ->join('users as u', 'pu.user_id', 'u.id')
                ->whereIn('status', ["Cancel by user", "Cancel by merchant", "Picked-Up"])
                ->where('organization_id', $org_id)
                ->select('pu.id', 'pu.pickup_date', 'pu.total_price', 'pu.status', 'pu.transaction_id',
                'u.name', 'u.telno')
                ->orderBy('pickup_date', 'asc')
                ->orderBy('pu.updated_at', 'desc');
        if(request()->ajax()) 
        {
            if(($filter_type == "" || $filter_type == "all") && $date == "") 
            {
                $order = $order->get();
            }
            else if($filter_type == "date")
            {
                $order = $order->whereBetween('pickup_date', 
                [Carbon::parse($date)->startOfDay()->toDateTimeString(),Carbon::parse($date)->endOfDay()->toDateTimeString()])->get();
            }
            else if($filter_type == "today")
            {
                $order->whereBetween('pickup_date', 
                [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])->get();
            }
            else if($filter_type == "week")
            {
                $order->whereBetween('pickup_date', 
                [Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()])->get();
            }
            else if($filter_type == "month")
            {
                $order->whereBetween('pickup_date', 
                [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfMonth()->toDateTimeString()])->get();
            }  

            $table = Datatables::of($order);
            $table->addColumn('status', function ($row) {
                if ($row->status == "Picked-Up") {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya Diambil</span>';
                    return $btn;
                } else if($row->status == "Cancel by user") {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Dibatalkan Oleh Pelanggan</span>';
                    return $btn;
                } else if($row->status == "Cancel by merchant") {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Dibatalkan Oleh Peniaga</span>';
                    return $btn;
                }
            });

            $table->editColumn('total_price', function ($row) {
                $amount = DB::table('transactions')->where('id' , $row->transaction_id)->pluck('amount')->first();
                $total_price = number_format($amount, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('admin-reg.order-detail', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y h:i A');
            });

            $table->rawColumns(['total_price', 'status']);

            return $table->make(true);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->o_id;

        $update_order = PgngOrder::find($id)->update([
            'status' => "Cancel by merchant",
            'deleted_at'=> Carbon::now(),
        ]);
        
        $cart = ProductOrder::where('pgng_order_id', $id)->update([
            'deleted_at'=> Carbon::now(),
        ]);
        
        if($update_order&& $cart) {
            Session::flash('success', 'Pesanan Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    public function showList($id)
    {
        // Get Information about the order
        $list = DB::table('pgng_orders as pu')
                ->join('users as u', 'u.id', '=', 'pu.user_id')
                ->where('pu.id', $id)
                ->where('pu.status', '!=' , 'In cart')
                ->select('pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status','pu.confirm_picked_up_time','pu.confirm_by',
                        'u.name', 'u.telno', 'u.email')
                ->first();

        $order_date = Carbon::parse($list->updated_at)->format('d/m/y H:i A');
        $pickup_date = Carbon::parse($list->pickup_date)->format('d/m/y H:i A');
        $total_order_price = number_format($list->total_price, 2, '.', '');
        $confirm_picked_up_time = Carbon::parse($list->confirm_picked_up_time)->format('d/m/y H:i A');
        $confirm_by = DB::table('users')
        ->where('id',$list->confirm_by)
        ->pluck('name')
        ->first();
        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', '=', 'pi.id')
                ->where([
                    ['po.pgng_order_id', $id],
                ])
                ->select('po.id', 'pi.name', 'pi.price', 'po.quantity')
                ->get();
        $total_price[] = array();
        $price[] = array();
        
        $amount = DB::table('pgng_orders as pu')
        ->join('transactions as t' , 't.id' ,'pu.transaction_id')
        ->where([
            'pu.id' => $id
        ])
        ->pluck('amount')
        ->first();

        $amount = number_format($amount,2 );
            
        foreach($item as $row)
        {
            $price[$row->id] = number_format($row->price, 2, '.', '');
            $total_price[$row->id] = number_format(doubleval($row->price * $row->quantity), 2, '.', ''); // calculate total for each item in cart
        }

        return view('merchant.regular.admin.list', compact('list', 'order_date', 'pickup_date', 'total_order_price', 'item', 'price', 'total_price','confirm_picked_up_time','confirm_by', 'amount'));
    }
}
