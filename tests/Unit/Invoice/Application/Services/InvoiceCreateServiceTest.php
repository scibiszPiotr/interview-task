<?php

namespace Tests\Unit\Invoice\Application\Services;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Invoices\Application\DTOs\InvoiceCreateDto;
use Modules\Invoices\Application\DTOs\InvoiceProductLineCreateDto;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Models\InvoiceFactory;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Modules\Invoices\Domain\Models\InvoiceProductLineFactory;
use Modules\Invoices\Domain\Repositories\InvoiceProductLineRepositoryInterface;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class InvoiceCreateServiceTest extends TestCase
{
    private InvoiceProductLineRepositoryInterface&MockObject $invoiceProductLineRepository;
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->invoiceProductLineRepository = $this->createMock(InvoiceProductLineRepositoryInterface::class);
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
    }

    public function testCreateInvoiceWithoutProducts(): void
    {
        $this->invoiceRepository->expects($this->once())->method('save');
        $this->invoiceProductLineRepository->expects($this->any())->method('save');

        $service = new InvoiceService(
            new InvoiceFactory,
            new InvoiceProductLineFactory,
            $this->invoiceRepository,
            $this->invoiceProductLineRepository,
        );

        $id = $service->create(new InvoiceCreateDto(
            'name',
            'email',
            []
        ));

        $this->assertNotEmpty($id);
    }

    public function testCreateInvoiceWithProducts(): void
    {
        $this->invoiceRepository->expects($this->once())->method('save');
        $this->invoiceProductLineRepository->expects($this->once())->method('save');

        $service = new InvoiceService(
            new InvoiceFactory,
            new InvoiceProductLineFactory,
            $this->invoiceRepository,
            $this->invoiceProductLineRepository,
        );

        $id = $service->create(new InvoiceCreateDto(
            'name',
            'email',
            [
                new InvoiceProductLineCreateDto(
                    'name',
                    1,
                    1
                )
            ]
        ));

        $this->assertNotEmpty($id);
    }

    public function testReturnDtoWithAllNecessaryFields(): void
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

        $this->invoiceRepository->expects($this->once())->method('getById')->with($id)->willReturn(
            $invoice
        );

        $service = new InvoiceService(
            new InvoiceFactory,
            new InvoiceProductLineFactory,
            $this->invoiceRepository,
            $this->invoiceProductLineRepository,
        );

        $dto = $service->get($id);

        $this->assertSame((string) $id, $dto->id);
        $this->assertSame('name', $dto->customerName);
        $this->assertSame('email', $dto->customerEmail);
        $this->assertSame('draft', $dto->status);
        $this->assertSame('Product test', $dto->products[0]->name);
        $this->assertSame(100, $dto->products[0]->price);
        $this->assertSame(2, $dto->products[0]->quantity);
        $this->assertSame(200, $dto->products[0]->totalUnitPrice);
        $this->assertSame(230, $dto->totalPrice);
    }
}
