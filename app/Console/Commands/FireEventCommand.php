<?php
namespace App\Console\Commands;

use App\Jobs\ProductCreated;
use App\Order;
use App\Product;
use Illuminate\Console\Command;

class FireEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fire';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $order = Order::find(1);
        // $data = $order->toArray();

        // $data['admin_total'] = $order->admin_total;
        // $data['influencer_total'] = $order->influencer_total;
        $producnt = Product::find(1);

        ProductCreated::dispatch($producnt->toArray())->onQueue('checkout_queue');
    }
}
