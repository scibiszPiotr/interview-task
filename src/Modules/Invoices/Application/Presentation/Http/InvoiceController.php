<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Modules\Invoices\Application\DTOs\InvoiceCreateDto;
use Modules\Invoices\Application\DTOs\InvoiceProductLineCreateDto;
use Modules\Invoices\Application\Presentation\Http\Request\CreateInvoiceRequest;
use Modules\Invoices\Application\Services\InvoiceService;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

final readonly class InvoiceController
{
    public function __construct(private InvoiceService $invoiceCreateService) {
    }

    public function create(CreateInvoiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $id = $this->invoiceCreateService->create(
            new InvoiceCreateDto(
                $validated['customerName'],
                $validated['customerEmail'],
                collect($validated['products'])
                    ->map(fn ($line) => new InvoiceProductLineCreateDto(
                        name: $line['name'],
                        price: $line['price'],
                        quantity: $line['quantity']
                    ))->toArray()
            ),
        );

        return new JsonResponse(data: ['id' => $id], status: Response::HTTP_CREATED);
    }

    public function view(string $id): JsonResponse
    {
        return new JsonResponse(data: $this->invoiceCreateService->get(Uuid::fromString($id)), status: Response::HTTP_OK);
    }
}
