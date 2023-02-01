<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Validator\Constraints\Length;
use Yajra\DataTables\DataTables;

class ReportController extends Controller
{
    public function index()
    {
        $merchant = RegularMerchantController::getAllMerchantOrganization();
        
        return view('merchant.regular.admin.report.index', compact('merchant'));
    }

    public function getReport(Request $request)
    {
        $org_id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $group = null;
        $chart = null;
        $total_order = 0;
        $total_sales = 0;
        $avg_sales = 0;

        $order_id = $this->getOrderId($org_id, $start_date, $end_date);
        
        if(count($order_id)){
            $group = $this->getProductGroupSales($org_id, $order_id);
            $chart = $this->getAllTransaction($order_id);
            $total_order = $this->getTotalOrder($order_id);
            $total_sales = $this->getTotalSales($order_id);
            $avg_sales = $this->getAvgSales($order_id);

            $total_sales = number_format($total_sales, 2);
            $avg_sales = number_format($avg_sales, 2);
        }

        $resp = (object)[
            'group' => $group,
            'chart' => $chart,
            'order' => $total_order,
            'sales' => $total_sales,
            'avgSales' => $avg_sales,
            'startDate' => Carbon::parse($start_date)->toDateString(),
            'endDate' => Carbon::parse($end_date)->toDateString(),
        ];

        return response()->json($resp);
    }

    public function showProductItemReport(Request $request, $group_id)
    {
        $quantity_sold = $request->quantitySold;
        $total_sales = number_format($request->totalSales, 2);

        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $group_name = DB::table('product_group')->where('id', $group_id)->first()->name;
            
        return view('merchant.regular.admin.report.product', compact('group_id', 'group_name', 'quantity_sold', 'total_sales', 'start_date', 'end_date'));
    }

    public function getOrderId($org_id, $start_date, $end_date)
    {
        $order_id = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->where('order_type', 'Pick-Up')
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->pluck('id')->toArray();

        if($start_date != '' && $end_date != ''){
            $formatted_start_date = Carbon::parse($start_date)->startOfDay()->toDateTimeString();
            $formatted_end_date = Carbon::parse($end_date)->endOfDay()->toDateTimeString();

            $order_id = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->where('order_type', 'Pick-Up')
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->whereBetween('pickup_date', [$formatted_start_date, $formatted_end_date])
                ->pluck('id')->toArray();
        }

        return $order_id;
    }

    public function getProductGroupTable(Request $request)
    {
        $group = $request->group;

        if(request()->ajax()) 
        {   
            $table = Datatables::of($group);

            $table->editColumn('totalSales', function ($row) {
                return number_format($row['totalSales'], 2);
            });

            $table->addColumn('action', function ($row) {
                $start_date = request()->start_date;
                $end_date = request()->end_date;
                $link = route('admin-reg.item-report', ['g_id' => $row['id'], 'quantitySold' => $row['quantitySold'], 'totalSales' => $row['totalSales'], 'start_date' => $start_date, 'end_date' => $end_date]);
                $btn = '<a href='.$link.' class="btn btn-primary"><i class="fas fa-pencil-alt"></i></a>';
                return $btn;
            });

            $table->rawColumns(['totalSales', 'action']);

            return $table->make(true);
        }
    }

    public function getProductGroupSales($org_id, $order_id)
    {
        $total_quantity = 0;
        $total_sales = 0;
        $group_name = array();
        $group_quantity = array();
        $group_sales = array();
        $group_arr = array();

        $groups = DB::table('product_group')
                ->where('organization_id', $org_id)
                ->select('id', 'name')
                ->get();

        $items = DB::table('pgng_orders as pu')
                ->join('product_order as po', 'pu.id', 'po.pgng_order_id')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->whereIn('pu.id', $order_id)
                ->select('po.quantity', 'po.selling_quantity', 'pi.price', 'pi.product_group_id')
                ->get();

        foreach($groups as $group){
            $total_quantity = 0;
            $total_sales = 0;
            foreach($items as $item){
                if($item->product_group_id == $group->id){
                    $total_quantity += $item->quantity * $item->selling_quantity;
                    $total_sales += ($item->quantity * $item->selling_quantity) * $item->price;
                }
            }
            $group_name[$group->id] = $group->name;
            $group_quantity[$group->id] = $total_quantity;
            $group_sales[$group->id] = $total_sales;
        }

        foreach($groups as $row)
        {
            $group_arr[] = [
                'id' => $row->id,
                'name' => $group_name[$row->id],
                'quantitySold' => $group_quantity[$row->id],
                'totalSales' => $group_sales[$row->id]
            ];
        }

        return (object)['group_arr' => (object)$group_arr, 'name' => $group_name, 'quantitySold' => $group_quantity, 'totalSales' => $group_sales];
    }

    public function getAllTransaction($order_id)
    {   
        $transac = DB::table('pgng_orders as pu')
                ->join('users as u', 'pu.user_id', 'u.id')
                ->whereIn('pu.id', $order_id)
                ->select('u.name', 'pu.pickup_date', 'pu.total_price')
                ->get();
        
        return $transac;
    }

    public function getTotalOrder($order_id)
    {
        $total_order = DB::table('pgng_orders')
                ->whereIn('id', $order_id)
                ->count();

        return $total_order;
    }

    public function getTotalSales($order_id)
    {
        $total_order = DB::table('pgng_orders')
                ->whereIn('id', $order_id)
                ->sum('total_price');

        return $total_order;
    }

    public function getAvgSales($order_id)
    {
        $avg_order = DB::table('pgng_orders')
                ->whereIn('id', $order_id)
                ->avg('total_price');
        
        return $avg_order;
    }

    public function getProductItemReport(Request $request)
    {
        $group_id = $request->group_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $item_name = array();
        $item_quantity = array();
        $item_sales = array();
        $item_arr = array();

        $org_id = DB::table('product_group')->where('id', $group_id)->first()->organization_id;
        
        $items = DB::table('product_item')
        ->where('product_group_id', $group_id)
        ->select('id', 'name')
        ->get();
        
        $order_id = $this->getOrderId($org_id, $start_date, $end_date);
        
        $carts = DB::table('pgng_orders as pu')
                ->join('product_order as po', 'pu.id', 'po.pgng_order_id')
                ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                ->whereIn('pu.id', $order_id)
                ->select('po.product_item_id', 'po.quantity', 'po.selling_quantity', 'pi.price')
                ->get();
        
        foreach($items as $item){
            $total_quantity = 0;
            $total_sales = 0;
            foreach($carts as $cart){
                if($item->id == $cart->product_item_id){
                    $total_quantity += $cart->quantity * $cart->selling_quantity;
                    $total_sales += ($cart->quantity * $cart->selling_quantity) * $cart->price;
                }
            }
            $item_name[$item->id] = $item->name;
            $item_quantity[$item->id] = $total_quantity;
            $item_sales[$item->id] = $total_sales;
        }
        
        foreach($items as $row)
        {
            $item_arr[] = [
                'id' => $row->id,
                'name' => $item_name[$row->id],
                'quantitySold' => $item_quantity[$row->id],
                'totalSales' => $item_sales[$row->id]
            ];
        }
        return response()->json(['item_arr' => (object)$item_arr]);
    }

    public function getProductItemTable(Request $request)
    {
        $item = $request->item;

        if(request()->ajax()) 
        {   
            $table = Datatables::of($item);

            $table->editColumn('totalSales', function ($row) {
                return number_format($row['totalSales'], 2);
            });

            $table->rawColumns(['totalSales']);

            return $table->make(true);
        }
    }
}
