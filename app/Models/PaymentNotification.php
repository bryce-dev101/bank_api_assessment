<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'amount',
        'merchant_id',
        'token',
        'signature',
        'billing_date',
        'payment_id'
    ];

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }
}
