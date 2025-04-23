<?php

namespace Modules\Invoices\Domain\Repositories;

use Modules\Invoices\Domain\Models\Invoice;
use Ramsey\Uuid\UuidInterface;

interface InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void;

    public function getById(UuidInterface $id): Invoice;
}
