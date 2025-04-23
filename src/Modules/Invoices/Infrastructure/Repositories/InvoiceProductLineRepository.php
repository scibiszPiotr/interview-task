<?php

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Modules\Invoices\Domain\Repositories\InvoiceProductLineRepositoryInterface;

class InvoiceProductLineRepository implements InvoiceProductLineRepositoryInterface
{
    public function save(InvoiceProductLine $invoiceProductLine): void
    {
        $invoiceProductLine->save();
    }
}
