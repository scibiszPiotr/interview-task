<?php

namespace Tests\Unit\Invoice\Application\Listeners;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Application\Listeners\ResourceDeliveredEventHandler;
use Modules\Invoices\Application\Listeners\SendInvoiceValidator;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
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
        private SendInvoiceValidator $sendInvoiceValidator;

    public function setUp(): void
    {
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $this->sendInvoiceValidator = $this->createMock(SendInvoiceValidator::class);
    }

    public function testHandlerNotFoundInvoice(): void
    {
        $handler  = new ResourceDeliveredEventHandler(
            $this->invoiceRepository,
            $this->notificationFacade,
            $this->invoiceService,
            $this->sendInvoiceValidator,
        );
        $this->invoiceRepository->method('getById')->willThrowException(new ModelNotFoundException);

        $handler->handle(new ResourceDeliveredEvent(Uuid::uuid4()));
        $this->expectNotToPerformAssertions();
    }

    public function testHandler(): void
    {
        $id = Uuid::uuid4();

        $invoice = $this->getMockBuilder(Invoice::class)
            ->onlyMethods(['productLines'])
            ->getMock();
        $invoice->id = (string) $id;
        $invoice->customer_name = 'name';
        $invoice->customer_email = 'email';
        $invoice->status = 'draft';
        $productLine = new InvoiceProductLine([
            'id' => (string) Uuid::uuid4(),
            'name' => 'Product test',
            'price' => 100,
            'quantity' => 2,
        ]);
        $productLineSecond = new InvoiceProductLine([
            'id' => (string) Uuid::uuid4(),
            'name' => 'Product A',
            'price' => 30,
            'quantity' => 1,
        ]);

        $hasManyMock = $this->createMock(HasMany::class);
        $hasManyMock->method('get')->willReturn(collect([$productLine, $productLineSecond]));

        $invoice->method('productLines')->willReturn($hasManyMock);

        $this->invoiceRepository->method('getById')->willReturn($invoice);
        $this->invoiceRepository->expects($this->exactly(2))->method('save');
        $this->notificationFacade->expects($this->once())->method('notify');
        $this->sendInvoiceValidator->expects($this->once())->method('canBySend');

        $handler  = new ResourceDeliveredEventHandler(
            $this->invoiceRepository,
            $this->notificationFacade,
            $this->invoiceService,
            $this->sendInvoiceValidator,
        );

        $handler->handle(new ResourceDeliveredEvent($id));

        $this->assertSame('sent-to-client', $invoice->status);
    }
}
