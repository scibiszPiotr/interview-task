<?php

namespace Modules\Invoices\Application\DTOs;

class InvoiceProductLineDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $price,
        public readonly int $quantity,
        public readonly int $totalUnitPrice,
    ) {
    }
}
