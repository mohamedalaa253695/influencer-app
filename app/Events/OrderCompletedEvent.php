<?php
namespace App\Events;

use App\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order ;

    public function __construct(Order $order)
    {
        $this->order = $order ;
    }
}
