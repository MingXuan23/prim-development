<?php

namespace App\Mail;

use App\User;
use App\Models\Transaction;
use App\Models\Organization;
use App\Models\PgngOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;

class MerchantOrderReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct(PgngOrder $order, Organization $organization, Transaction $transaction, User $user)
    {
        $this->order = $order;
        $this->item = DB::table('product_order as po')
                    ->join('product_item as pi', 'po.product_item_id', 'pi.id')
                    ->where('po.pgng_order_id', $order->id)
                    ->select('pi.name', 'po.quantity', 'po.selling_quantity', 'pi.price')
                    ->get();
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
        $order = $this->order;
        $item = $this->item;
        $organization = $this->organization;
        $transaction = $this->transaction;
        $user = $this->user;

        return $this->view('merchant.receipt', compact('order', 'item', 'organization', 'transaction', 'user'));
    }
}
