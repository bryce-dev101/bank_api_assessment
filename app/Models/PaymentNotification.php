<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pf_payment_id',
        'payment_status',
        'item_name',
        'item_description',
        'amount_gross',
        'amount_fee',
        'amount_net',
        'merchant_id',
        'name_first',
        'name_last',
        'email_address',
        'signature',
        'payment_id'
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
