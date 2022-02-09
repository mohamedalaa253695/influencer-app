<?php
namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Support\Facades\Mail;

class NotifyAdminListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCompletedListener  $event
     * @return void
     */
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;

        Mail::send('influencer.admin-email', ['order' => $order], function ($message) {
            $message->to('admin@admin.com', 'Admin');

            $message->subject('A new order has been compeleted');
        });
    }
}
