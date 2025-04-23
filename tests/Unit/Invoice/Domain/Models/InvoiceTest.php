<?php

namespace Tests\Unit\Invoice\Domain\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Application\DTOs\InvoiceCreateDto;
use Modules\Invoices\Application\DTOs\InvoiceProductLineCreateDto;
use Modules\Invoices\Domain\Exceptions\StatusTransitionException;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Models\InvoiceFactory;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    public function testTotalPrice()
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

        $this->assertSame(230, $invoice->getInvoiceTotalPrice());
    }

    public function testMarkAsSending(): void
    {
        $invoice = new Invoice(
            [
                'id' => Uuid::uuid4(),
                'customer_name' => 'name',
                'customer_email' => 'email',
                'status' => 'draft',
            ]
        );

        $this->assertSame('draft', $invoice->status);

        $invoice->markAsSending();

        $this->assertSame('sending', $invoice->status);
    }

    public function testOnlyFromDraftAllowMarkAsSending(): void
    {
        $invoice = new Invoice(
            [
                'id' => Uuid::uuid4(),
                'customer_name' => 'name',
                'customer_email' => 'email',
                'status' => 'draft',
            ]
        );

        $this->assertSame('draft', $invoice->status);

        $invoice->markAsSending();

        $this->expectException(StatusTransitionException::class);
        $this->expectExceptionMessage('Invoice is already marked as sending');

        $invoice->markAsSending();
    }

    public function testMarkAsSendToCustomer(): void
    {
        $invoice = new Invoice(
            [
                'id' => Uuid::uuid4(),
                'customer_name' => 'name',
                'customer_email' => 'email',
                'status' => 'draft',
            ]
        );

        $this->assertSame('draft', $invoice->status);
        $invoice->markAsSending();
        $invoice->markAsSentToClient();

        $this->assertSame('sent-to-client', $invoice->status);
    }

    public function testOnlyFromDraftAllowMarkAsSendToClient(): void
    {
        $invoice = new Invoice(
            [
                'id' => Uuid::uuid4(),
                'customer_name' => 'name',
                'customer_email' => 'email',
                'status' => 'draft',
            ]
        );

        $this->assertSame('draft', $invoice->status);
        $invoice->markAsSending();
        $invoice->markAsSentToClient();

        $this->expectException(StatusTransitionException::class);
        $this->expectExceptionMessage('Invoice is already marked as sent to client');

        $invoice->markAsSentToClient();
    }
}
