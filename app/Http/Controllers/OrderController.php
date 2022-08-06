<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Organization;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Null_;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function orderTransaction(Request $request)
    {
        try {

           $this->validate($request, [
                'delivery_status'       => 'required',
                'user_id'               => 'required',
                'organ_id'              => 'required',
                'dish_available_id'     => 'required',
                'order_dish'            => 'required'
           ]);

            $order = new Order();
            $order->delivery_status = $request->delivery_status;
            $order->organ_id = $request->organ_id;
            $order->dish_available_id = $request->dish_available_id;
            $order->user_id = $request->user_id;
            $order->order_description = $request->order_description;
            $order->save();

            foreach ($request->order_dish as $order_dish) {
                DB::table('order_dish')->insert([
                    'quantity'  => $order_dish['quantity'],
                    'order_id'  => $order->id,
                    'dish_id'   => $order_dish['dish_id'],
                ]);
            }

            $order_dishes = DB::table('order_dish as od')
                ->leftJoin('dishes as d', 'd.id', 'od.dish_id')
                ->leftJoin('orders as o', 'o.id', 'od.order_id')
                ->where('od.order_id', $order->id)
                ->orderBy('d.name')
                ->get();

            $organization = Organization::find($request->organ_id);
            $user = User::find($request->user_id);

            // dd($order_dishes, $organization);

            $banklists = FPXController::getStaticBankList();

            return view('order.order-pay', compact('order_dishes', 'organization', 'order', 'user', 'banklists'));
            
        } catch (\Throwable $th) {
            return response($th->getMessage())->setStatusCode(401);
        }
    }
}
