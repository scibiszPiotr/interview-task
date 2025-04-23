<?php

namespace Modules\Invoices\Infrastructure\Repositories;

use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Ramsey\Uuid\UuidInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void
    {
        $invoice->save();
    }

    public function getById(UuidInterface $id): Invoice
    {
        return Invoice::with('productLines')->findOrFail((string) $id);
    }
}
