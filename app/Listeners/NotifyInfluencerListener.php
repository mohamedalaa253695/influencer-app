<?php
namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use Illuminate\Support\Facades\Mail;

class NotifyInfluencerListener
{
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;
        Mail::send('influencer.influencer-email', ['order' => $order], function ($message) use ($order) {
            $message->to($order->influencer_email, 'Admin');
            $message->subject('A new order has been compeleted');
        });
    }
}
