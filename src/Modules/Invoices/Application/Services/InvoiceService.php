<?php

namespace Modules\Invoices\Application\Services;

use Modules\Invoices\Application\DTOs\InvoiceCreateDto;
use Modules\Invoices\Application\DTOs\InvoiceDto;
use Modules\Invoices\Application\DTOs\InvoiceProductLineDto;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Models\InvoiceFactory;
use Modules\Invoices\Domain\Models\InvoiceProductLineFactory;
use Modules\Invoices\Domain\Repositories\InvoiceProductLineRepositoryInterface;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Ramsey\Uuid\UuidInterface;

readonly class InvoiceService
{
    public function __construct(
        private InvoiceFactory $invoiceFactory,
        private InvoiceProductLineFactory $invoiceProductLineFactory,
        private InvoiceRepositoryInterface $invoiceRepository,
        private InvoiceProductLineRepositoryInterface $invoiceProductLineRepository,
    ) {
    }

    public function create(InvoiceCreateDto $request): string
    {
        $invoice = $this->invoiceFactory->create($request);
        $this->invoiceRepository->save($invoice);

        if (!empty($request->products)) {
            foreach ($request->products as $product) {
                $productLine = $this->invoiceProductLineFactory->create(
                    $invoice->id,
                    $product
                );

                $this->invoiceProductLineRepository->save($productLine);
            }
        }

        return $invoice->id;
    }

    public function get(UuidInterface $id): InvoiceDto
    {
        /** @var Invoice $invoice */
        $invoice = $this->invoiceRepository->getById($id);

        $productLines = [];
        foreach ($invoice->productLines()->get() as $productLine) {
            $productLines[] = new InvoiceProductLineDto(
                $productLine->id,
                $productLine->name,
                $productLine->price,
                $productLine->quantity,
                $productLine->getTotalPrice(),
            );
        }

        return new InvoiceDto(
            $invoice->id,
            $invoice->customer_name,
            $invoice->customer_email,
            $invoice->status,
            $productLines,
            $invoice->created_at,
            $invoice->getInvoiceTotalPrice(),
        );
    }


}
