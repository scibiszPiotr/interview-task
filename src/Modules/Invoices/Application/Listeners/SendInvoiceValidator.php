<?php

namespace Modules\Invoices\Application\Listeners;

use Modules\Invoices\Application\Exceptions\ValidationExceptions;
use Modules\Invoices\Domain\Models\InvoiceProductLine;

class SendInvoiceValidator
{
    /** @var InvoiceProductLine[] $productsLine */
    public function canBySend($productsLine): void
    {
        if (count($productsLine) === 0) {
            throw new ValidationExceptions('Product line is required for sending invoices');
        }

        foreach ($productsLine as $productLine) {
            if ($productLine->getTotalPrice() === 0) {
                throw new ValidationExceptions('Product line have price 0 or quantity is 0');
            }
        }
    }
}
