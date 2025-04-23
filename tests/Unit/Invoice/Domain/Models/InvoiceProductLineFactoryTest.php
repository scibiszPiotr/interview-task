<?php

namespace Tests\Unit\Invoice\Domain\Models;

use Modules\Invoices\Application\DTOs\InvoiceProductLineCreateDto;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Modules\Invoices\Domain\Models\InvoiceProductLineFactory;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class InvoiceProductLineFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new InvoiceProductLineFactory();

        $invoiceProductLine = $factory->create(
            (string) Uuid::uuid4(),
            new InvoiceProductLineCreateDto(
                'name',
                100,
                3
            )
        );

        $this->assertInstanceOf(InvoiceProductLine::class, $invoiceProductLine);
        $this->assertSame(3, $invoiceProductLine->quantity);
        $this->assertSame(100, $invoiceProductLine->price);
        $this->assertSame('name', $invoiceProductLine->name);
    }
}
