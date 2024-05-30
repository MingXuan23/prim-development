<?php

namespace App\Http\Controllers\Merchant\Regular;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;
use App\Models\PgngOrder;

class HistoryController extends Controller
{
    public function index()
    {
        return view('merchant.order');
    }

    public function getAllOrder(Request $request)
    {
        $total_price[] = 0;
        $pickup_date[] = 0;
        $status = ['Paid'];

        $order = $this->getAllOrderQuery($status)->get();

        if(request()->ajax())
        {
            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == 'Paid') {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>';
                    return $btn;
                } else if($row->status == 'Cancel by user'){
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>';
                    return $btn;
                }
            });

            // $table->addColumn('action', function ($row) {
            //     $btn = '<div class="d-flex justify-content-center align-items-center">';
            //     $btn = $btn.'<button type="button" class="btn-cancel-order btn btn-danger" data-order-id="'.$row->id.'">';
            //     $btn = $btn.'<i class="fas fa-trash-alt"></i></button></div>';

            //     return $btn;
            // });

            // $table->editColumn('note', function ($row) {
            //     if($row->note != null) {
            //         return $row->note;
            //     } else {
            //         return "<i>Tiada Nota</i>";
            //     }
            // });
            $table->addColumn('action', function ($row) {
                $btn = '<div class="d-flex justify-content-center align-items-center">';
                $btn = $btn.'<button type="button" class="btn-done-pickup btn btn-primary mr-2" data-order-id="'.$row->id.'"><i class="fas fa-clipboard-check"></i></button>';
                $btn = $btn.'<button type="button" class="btn-cancel-order btn btn-danger" data-order-id="'.$row->id.'">';
                $btn = $btn.'<i class="fas fa-trash-alt"></i></button></div>';

                return $btn;
            });

            $table->editColumn('total_price', function ($row) {
                $total_price = number_format($row->total_price, 2, '.', '');
                $total = $total_price." | ";
                $total = $total."<a href='".route('merchant.order-detail', $row->id)."'>Lihat Pesanan</a>";
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/Y H:i A');
            });

            $table->rawColumns(['total_price', 'status','action']);

            return $table->make(true);
        }
    }
    public function orderPickedUp(Request $request)
    {
        $update_order = DB::table('pgng_orders')
        ->where('id',$request->o_id)
        ->update([
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
    public function deletePaidOrder(Request $request)
    {
        $id = $request->o_id;

        $delete_order = DB::table('pgng_orders')->where('id', $id)->update([
            'status' => 'Cancel by user',
            'deleted_at' => Carbon::now(),
        ]);
        $cart = DB::table('product_order')
        ->where('pgng_order_id', $id)
        ->update([
            'deleted_at'=> Carbon::now(),
        ]);
        if($delete_order &&  $cart) {
            Session::flash('success', 'Pesanan Berjaya Dibuang');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Pesanan Gagal Dibuang');
            return View::make('layouts/flash-messages');
        }
    }

    public function history()
    {
        return view('merchant.history');
    }

    public function getOrderHistory(Request $request)
    {
        $total_price[] = 0;
        $pickup_date[] = 0;
        $status = ['Failed', 'Pending', 'Cancel by user', 'Cancel by merchant', 'Delivered', 'Picked-Up'];
        $filter_type = $request->filterType;
        $date = $request->date;

        $order = $this->getAllOrderQuery($status);

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

            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == 'Picked-Up') {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya diambil</span>';
                    return $btn;
                } else if ($row->status == 'Cancel by user'){
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Dibatalkan oleh pembeli</span>';
                    return $btn;
                } else if($row->status == 'Cancel by merchant'){
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Dibatalkan oleh penjual</span>';
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
                return Carbon::parse($row->pickup_date)->format('d/m/Y H:i A');
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
                ->leftJoin('users as u','u.id','pu.user_id')
                ->where('pu.id', $order_id)
                ->select('pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status','pu.confirm_picked_up_time','pu.confirm_by',
                        'pu.organization_id','o.nama', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state','o.district','o.city', 'o.fixed_charges','u.name as user_name')
                ->first();

        $order_date = Carbon::parse($list->updated_at)->format('d/m/y H:i A');
        $pickup_date = Carbon::parse($list->pickup_date)->format('d/m/y H:i A');
        $total_order_price = number_format($list->total_price, 2);
        $confirm_picked_up_time = Carbon::parse($list->confirm_picked_up_time)->format('d/m/y H:i A');
        $confirm_by = DB::table('users')
        ->where('id',$list->confirm_by)
        ->pluck('name')
        ->first();
        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->where([
                    ['po.pgng_order_id', $order_id],
                ])
                ->select('po.id', 'pi.name', 'pi.price', 'po.quantity')
                ->get();

        $total_price[] = array();
        $price[] = array();

        foreach($item as $row)
        {
            $price[$row->id] = number_format($row->price, 2);
            $total_price[$row->id] = number_format(doubleval($row->price * $row->quantity), 2); // calculate total for each item in cart
        }
        $receipt_no = PgngOrder::with('transaction')->find($order_id)->transaction->nama;

        $org_charge = ProductController::getFixedCharges($list->organization_id ,$list->total_price);
        return view('merchant.list', compact('list', 'order_date', 'pickup_date', 'total_order_price', 'item', 'price', 'total_price','confirm_picked_up_time','confirm_by' , 'receipt_no' ,'org_charge'));
    }

    public function showOrderDetailTransaction($trasanction_id)
    {

        $order_id = DB::table('pgng_orders')
                    //->where('status','Paid')
                    ->where('transaction_id',$trasanction_id)
                    ->first()
                    ->id;
        // Get Information about the order
        $list = DB::table('pgng_orders as pu')
                ->join('organizations as o', 'o.id', 'pu.organization_id')
                ->leftJoin('users as u','u.id','pu.user_id')
                ->where('pu.id', $order_id)
                ->select('pu.updated_at', 'pu.pickup_date', 'pu.total_price', 'pu.note', 'pu.status','pu.confirm_picked_up_time','pu.confirm_by',
                        'pu.organization_id','o.nama', 'o.telno', 'o.email', 'o.address', 'o.postcode', 'o.state','o.district','o.city', 'o.fixed_charges','u.name as user_name')
                ->first();

        //delete in the futurre
        $list->status = 'Picked-Up';
        if (in_array($trasanction_id, [46726,47003])) {
            $list->user_name = "CHOONG HUI XIN";
        }

        //delete in the futurre

        $order_date = Carbon::parse($list->updated_at)->format('d/m/y H:i A');
        $pickup_date = Carbon::parse($list->pickup_date)->format('d/m/y H:i A');
        $total_order_price = number_format($list->total_price, 2);
        $confirm_picked_up_time = Carbon::parse($list->confirm_picked_up_time)->format('d/m/y H:i A');
        $confirm_by = DB::table('users')
        ->where('id',$list->confirm_by)
        ->pluck('name')
        ->first();
        // get all product based on order
        $item = DB::table('product_order as po')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->where([
                    ['po.pgng_order_id', $order_id],
                ])
                ->select('po.id', 'pi.name', 'pi.price', 'po.quantity')
                ->get();

        $total_price[] = array();
        $price[] = array();

        foreach($item as $row)
        {
            $price[$row->id] = number_format($row->price, 2);
            $total_price[$row->id] = number_format(doubleval($row->price * $row->quantity), 2); // calculate total for each item in cart
        }
        $receipt_no = PgngOrder::with('transaction')->find($order_id)->transaction->nama;

        $org_charge = ProductController::getFixedCharges($list->organization_id ,$list->total_price);
        return view('merchant.list', compact('list', 'order_date', 'pickup_date', 'total_order_price', 'item', 'price', 'total_price','confirm_picked_up_time','confirm_by' , 'receipt_no' ,'org_charge'));
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
                ->orderBy('pickup_date', 'desc')
                ->orderBy('pu.updated_at', 'desc');

        return $order;
    }
}
