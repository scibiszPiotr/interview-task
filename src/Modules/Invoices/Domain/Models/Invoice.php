<?php

namespace Modules\Invoices\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Exceptions\StatusTransitionException;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $customer_name
 * @property string $customer_email
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|InvoiceProductLine[] $productLines
 */
class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_name',
        'customer_email',
        'status',
    ];

    public function productLines(): HasMany
    {
        return $this->hasMany(InvoiceProductLine::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::uuid4();
            }
            $model->status = StatusEnum::Draft->value;
        });
    }

    public function getInvoiceTotalPrice(): int
    {
        $total = 0;

        foreach ($this->productLines()->get() as $productLine) {
            $total += $productLine->getTotalPrice();
        }

        return $total;
    }

    public function markAsSending(): void
    {
        if ($this->status !== StatusEnum::Draft->value) {
            throw new StatusTransitionException('Invoice is already marked as sending');
        }

        $this->status = StatusEnum::Sending->value;
    }

    public function markAsSentToClient(): void
    {
        if ($this->status !== StatusEnum::Sending->value) {
            throw new StatusTransitionException('Invoice is already marked as sent to client');
        }

        $this->status = StatusEnum::SentToClient->value;
    }
}
