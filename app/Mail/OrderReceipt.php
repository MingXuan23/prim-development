<?php

namespace App\Mail;

use App\User;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, Organization $organization, Transaction $transaction, User $user)
    {
        // new OrderReceipt($order_dishes, $organization, $transaction, $user)
        $this->order_dishes = DB::table('order_dish as od')
                ->leftJoin('dishes as d', 'd.id', 'od.dish_id')
                ->leftJoin('orders as o', 'o.id', 'od.order_id')
                ->where('od.order_id', $order->id)
                ->orderBy('d.name')
                ->get();

        $this->order = $order;
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
        $order_dishes = $this->order_dishes;
        $organization = $this->organization;
        $transaction = $this->transaction;
        $user = $this->user;

        return $this->view('order.receipt', compact('order_dishes', 'organization', 'transaction', 'user'));
    }
}
