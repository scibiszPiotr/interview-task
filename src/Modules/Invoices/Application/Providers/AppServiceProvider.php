<?php

namespace Modules\Invoices\Application\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Invoices\Domain\Repositories\InvoiceProductLineRepositoryInterface;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Invoices\Infrastructure\Repositories\InvoiceProductLineRepository;
use Modules\Invoices\Infrastructure\Repositories\InvoiceRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(InvoiceRepositoryInterface::class, function ($app) {
            return new InvoiceRepository();
        });

        $this->app->bind(InvoiceProductLineRepositoryInterface::class, function ($app) {
            return new InvoiceProductLineRepository();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
