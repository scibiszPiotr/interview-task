<?php

namespace Modules\Invoices\Domain\Models;

use Modules\Invoices\Application\DTOs\InvoiceCreateDto;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Ramsey\Uuid\Uuid;

class InvoiceFactory
{
    public function create(InvoiceCreateDto $invoiceCreateRequest): Invoice
    {
        return new Invoice([
            'id' => (string) Uuid::uuid4(),
            'customer_name' => $invoiceCreateRequest->customerName,
            'customer_email' => $invoiceCreateRequest->customerEmail,
            'status' => StatusEnum::Draft->value,
        ]);
    }
}
