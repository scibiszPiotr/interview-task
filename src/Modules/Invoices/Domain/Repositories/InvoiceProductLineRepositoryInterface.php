<?php

namespace Modules\Invoices\Domain\Repositories;

use Modules\Invoices\Domain\Models\InvoiceProductLine;

interface InvoiceProductLineRepositoryInterface
{
    public function save(InvoiceProductLine $invoiceProductLine): void;
}
