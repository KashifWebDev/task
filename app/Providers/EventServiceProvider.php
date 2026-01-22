<?php

namespace App\Providers;

use App\Events\OrderConfirmed;
use App\Listeners\BookShipment;
use App\Listeners\CapturePayment;
use App\Listeners\ReserveInventory;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderConfirmed::class => [
            ReserveInventory::class,
            CapturePayment::class,
            BookShipment::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
