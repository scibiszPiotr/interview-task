<?php

namespace Modules\Invoices\Domain\Models;

use Modules\Invoices\Application\DTOs\InvoiceProductLineCreateDto;
use Ramsey\Uuid\Uuid;

class InvoiceProductLineFactory
{
    public function create(string $invoiceId, InvoiceProductLineCreateDto $dto): InvoiceProductLine
    {
        return new InvoiceProductLine(
            [
                'id' => (string) Uuid::uuid4(),
                'invoice_id' => $invoiceId,
                'name' => $dto->name,
                'price' => $dto->price,
                'quantity' => $dto->quantity,
            ]
        );
    }
}
