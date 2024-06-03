<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'merchant_id',
        'amount',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    
    public function payment_notifications()
    {
        return $this->hasMany('App\Models\PaymentNotification');
    }
}
