<?php
namespace App\Listeners;

use App\User;
use App\Events\OrderCompletedEvent;
use Illuminate\Support\Facades\Redis;

class UpdateRankingsListener
{
    public function handle(OrderCompletedEvent $event)
    {
        $order = $event->order;
        $revenue = $order->influencer_total;
        $user = User::find($order->user_id);
        Redis::zincrby('rankings', $revenue, $user->full_name);
    }
}
