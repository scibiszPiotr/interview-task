<?php

namespace Modules\Invoices\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

/**
 * @property string $id
 * @property string $invoice_id
 * @property string $name
 * @property int $price
 * @property int $quantity
 */
class InvoiceProductLine extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'invoice_id',
        'name',
        'price',
        'quantity',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Uuid::uuid4();
            }
        });
    }

    public function getTotalPrice(): int
    {
        return $this->price * $this->quantity;
    }
}
