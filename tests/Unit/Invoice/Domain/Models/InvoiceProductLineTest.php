<?php

namespace Tests\Unit\Invoice\Domain\Models;

use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Tests\TestCase;

class InvoiceProductLineTest extends TestCase
{
    public function testTotalPrice(): void
    {
        $invoiceProductLine = new InvoiceProductLine(
            [
                'name' => 'test',
                'price' => 123,
                'quantity' => 2,
            ]
        );


        $this->assertSame(246, $invoiceProductLine->getTotalPrice());
    }
}
