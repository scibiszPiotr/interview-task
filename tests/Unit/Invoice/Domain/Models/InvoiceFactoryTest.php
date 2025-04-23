<?php

namespace Tests\Unit\Invoice\Domain\Models;

use Modules\Invoices\Application\DTOs\InvoiceCreateDto;
use Modules\Invoices\Domain\Models\Invoice;
use Modules\Invoices\Domain\Models\InvoiceFactory;
use Tests\TestCase;

class InvoiceFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new InvoiceFactory();
        $invoice = $factory->create(
            new InvoiceCreateDto(
                'name',
                'email',
                []
            )
        );

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('name', $invoice->customer_name);
        $this->assertEquals('email', $invoice->customer_email);
    }
}
