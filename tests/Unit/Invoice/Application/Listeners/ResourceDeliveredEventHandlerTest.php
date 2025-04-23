<?php

namespace Tests\Unit\Invoice\Application\Listeners;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Invoices\Application\Listeners\ResourceDeliveredEventHandler;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Modules\Notifications\Api\NotificationFacadeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ResourceDeliveredEventHandlerTest extends TestCase
{
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;
        private NotificationFacadeInterface&MockObject $notificationFacade;
        private InvoiceService&MockObject $invoiceService;

    public function setUp(): void
    {
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->notificationFacade = $this->createMock(NotificationFacadeInterface::class);
    }

    public function testHandlerNotFoundInvoice(): void
    {
        $handler  = new ResourceDeliveredEventHandler(
            $this->invoiceRepository,
            $this->notificationFacade,
            $this->invoiceService
        );
        $this->invoiceRepository->method('getById')->willThrowException(new ModelNotFoundException);

        $handler->handle(new ResourceDeliveredEvent(Uuid::uuid4()));
        $this->expectNotToPerformAssertions();
    }

    public function testHandler(): void
    {
        $invoice = new Invoice(
            [
                'id' => $id = Uuid::uuid4(),
                'customer_name' => 'name',
                'customer_email' => 'email',
                'status' => 'draft',
            ]
        );

        $this->invoiceRepository->method('getById')->willReturn($invoice);
        $this->invoiceRepository->expects($this->exactly(2))->method('save');
        $this->notificationFacade->expects($this->once())->method('notify');

        $handler  = new ResourceDeliveredEventHandler(
            $this->invoiceRepository,
            $this->notificationFacade,
            $this->invoiceService
        );

        $handler->handle(new ResourceDeliveredEvent($id));

        $this->assertSame('sent-to-client', $invoice->status);
    }
}
