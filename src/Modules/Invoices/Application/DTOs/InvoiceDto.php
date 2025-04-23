<?php

namespace Modules\Invoices\Application\DTOs;

use JsonSerializable;

readonly class InvoiceDto implements JsonSerializable
{
    /**
     * @param InvoiceProductLineCreateDto[] $products
     */
    public function __construct(
        public string $id,
        public string $customerName,
        public string $customerEmail,
        public string $status,
        public array  $products,
        public ?\DateTimeInterface $createdAt,
        public int $totalPrice,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'customerName' => $this->customerName,
            'customerEmail' => $this->customerEmail,
            'status' => $this->status,
            'products' => array_map(fn($p) => $p instanceof JsonSerializable ? $p->jsonSerialize() : $p, $this->products),
            'createdAt' => $this->createdAt?->format(DATE_ATOM),
            'totalPrice' => $this->totalPrice,
        ];
    }
}
