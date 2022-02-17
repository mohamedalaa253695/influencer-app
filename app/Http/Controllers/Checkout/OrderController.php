<?php
namespace App\Http\Controllers\Checkout;

use App\Events\OrderCompletedEvent;
use App\Jobs\OrderCompleted;
use App\Link;
use App\Order;
use App\OrderItem;
use App\Product;
use Cartalyst\Stripe\Stripe;
use DB;
use Illuminate\Http\Request;

class OrderController
{
    public function store(Request $request)
    {
        // dd('here');
        $link = Link::where('code', $request->input('code'))->first();
        // dd($link->user->email);
        DB::beginTransaction();
        $order = new Order();

        $order->first_name = $request->input('first_name');
        $order->last_name = $request->input('last_name');
        $order->email = $request->input('email');
        $order->code = $link->code;
        $order->user_id = $link->user->id;
        $order->influencer_email = $link->user->email;
        $order->address = $request->input('address');
        $order->address2 = $request->input('address2');
        $order->city = $request->input('city');
        $order->country = $request->input('country');
        $order->zip = $request->input('zip');
        $order->save();
        // dd($link->email);
        $line_items = [];
        foreach ($request->input('items') as $item) {
            $product = Product::find($item['product_id']);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_title = $product->title;
            $orderItem->price = $product->price;
            $orderItem->quantity = $item['quantity'];
            $orderItem->influencer_revenue = 0.1 * $product->price * $item['quantity'];
            $orderItem->admin_revenue = 0.9 * $product->price * $item['quantity'];

            $orderItem->save();

            $lineItems[] = [
                'name' => $product->title,
                'description' => $product->description,
                'images' => [
                    $product->image,
                ],
                'amount' => 100 * $product->price,
                'currency' => 'usd',
                'quantity' => $orderItem->quantity,
            ];
        }

        $stripe = Stripe::make(env('STRIPE_SECRET'));

        $source = $stripe->checkout()->sessions()->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => env('CHECKOUT_URL') . '/success?source={CHECKOUT_SESSION_ID}',
            'cancel_url' => env('CHECKOUT_URL') . '/error',
        ]);
        // dd('here');

        $order->transaction_id = $source['id'];
        $order->save();
        DB::commit();
        return $source;
    }

    public function confirm(Request $request)
    {
        if (!$order = Order::whereTransactionId($request->input('source'))->first()) {
            return response([
                'error' => 'Order not found!',
            ], 404);
        }
        $order->complete = 1 ;
        $order->save();

        // dd($order);
        event(new OrderCompletedEvent($order));
        $data = $order->toArray();
        $data['admin_total'] = $order->admin_total;
        $data['influencer_total'] = $order->influencer_total;
        OrderCompleted::dispatch($data);

        return response([
            'message' => 'success'
        ], 200);
    }
}
