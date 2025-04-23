<?php

namespace Modules\Invoices\Application\Listeners;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Models\InvoiceProductLine;
use Modules\Invoices\Domain\Repositories\InvoiceRepositoryInterface;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Ramsey\Uuid\Uuid;

class ResourceDeliveredEventHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private NotificationFacadeInterface $notificationFacade,
        private InvoiceService $invoiceService,
        private SendInvoiceValidator $sendInvoiceValidator,
    ) {
    }

    public function handle(ResourceDeliveredEvent $event): void
    {
        try {
            $invoice = $this->invoiceRepository->getById(Uuid::fromString($event->resourceId));
        } catch (ModelNotFoundException) {
            return;
        }

        $this->sendInvoiceValidator->canBySend($invoice->productLines()->get());

        $invoice->markAsSending();
        $this->invoiceRepository->save($invoice);

        $this->notificationFacade->notify(
            new NotifyData(
                Uuid::fromString($invoice->id),
                $invoice->customer_email,
                sprintf('Invoice for customer: %s', $invoice->customer_name),
                sprintf('Invoice content: %s', json_encode($this->invoiceService->get(Uuid::fromString($invoice->id))))
            )
        );

        $invoice->markAsSentToClient();
        $this->invoiceRepository->save($invoice);
    }
}
