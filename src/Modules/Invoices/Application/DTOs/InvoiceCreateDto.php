<?php

namespace Modules\Invoices\Application\DTOs;

final readonly class InvoiceCreateDto
{
    /**
     * @param InvoiceProductLineCreateDto[] $products
     */
    public function __construct(
        public string $customerName,
        public string $customerEmail,
        public array $products,
    ) {
    }
}
