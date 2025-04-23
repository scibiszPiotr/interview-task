<?php

namespace Modules\Invoices\Application\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Application\Listeners\ResourceDeliveredEventHandler;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(
            ResourceDeliveredEvent::class,
            ResourceDeliveredEventHandler::class
        );
    }
}
