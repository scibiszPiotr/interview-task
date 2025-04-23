<?php

namespace Tests\Unit\Invoice\Application\Listeners;

use Modules\Invoices\Application\Exceptions\ValidationExceptions;
use Modules\Invoices\Application\Listeners\SendInvoiceValidator;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Tests\TestCase;

class SendInvoiceValidatorTest extends TestCase
{
    public function testCanBySend(): void
    {
        $validator = new SendInvoiceValidator();

        $validator->canBySend([new InvoiceProductLine(
            [
                'name' => 'test',
                'quantity' => 1,
                'price' => 100,
            ]
        )]);

        $this->expectNotToPerformAssertions();
    }

    public function testCanBySendException(): void
    {
        $validator = new SendInvoiceValidator();

        $this->expectException(ValidationExceptions::class);

        $validator->canBySend([new InvoiceProductLine(
            [
                'name' => 'test',
                'quantity' => 0,
                'price' => 100,
            ]
        )]);
    }

    public function testEmptyListOfProducts(): void
    {
        $validator = new SendInvoiceValidator();

        $this->expectException(ValidationExceptions::class);

        $validator->canBySend([]);
    }
}
