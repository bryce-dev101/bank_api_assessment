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
        'order_number',
        'payment_method',
        'merchant_id',
        'amount',
        'payment_id'
    ];

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }
}
