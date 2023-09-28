<?php

namespace App\Mail;

use App\User;
use App\Models\Organization;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $orders, Organization $organization, Transaction $transaction, User $user)
    {
        $this->$booking_order = Organization::join('orders', 'organizations.id', '=', 'orders.organ_id')
        ->join('order_dish','order_dish.order_id','=','orders.id')
        ->join('dishes','dishes.id','=','order_dish.dish_id')
        ->where('orders.user_id', $userId)
        ->where('orders.id',$orderId)
        ->select('organizations.nama', 'organizations.address', 'dishes.name', 'order_dish.quantity', 'dishes.price','order_dish.updated_at', DB::raw('SUM(order_dish.quantity*dishes.price) as totalprice'))
        ->get();

        $this->orders = $orders;
        $this->organization = $organization;
        $this->transaction = $transaction;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $booking_order = $this->booking_order;
        $organization = $this->organization;
        $transaction = $this->transaction;
        $user = $this->user;

        return $this->view('orders.receipt', compact('booking_order', 'organization', 'transaction', 'user'));
    }
}
