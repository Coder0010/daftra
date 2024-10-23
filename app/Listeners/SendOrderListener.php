<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderListener
{

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        \Log::info('Order placed: ', ['order_id' => $event->order->id]);
    }
}
