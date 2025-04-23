<?php

namespace Modules\Invoices\Application\DTOs;

use JsonSerializable;

final readonly class InvoiceProductLineCreateDto implements JsonSerializable
{
    public function __construct(
        public string $name,
        public int $price,
        public int $quantity,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }
}
