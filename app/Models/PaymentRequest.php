<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_cell_number',
        'item_name',
        'item_description',
        'amount',
        'merchant_id',
        'merchant_key',
        'payment_id'
    ];

    public function payment()
    {
        return $this->belongsTo('App\Models\Payment');
    }
}
