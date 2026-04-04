<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonusPayment extends Model
{
    protected $fillable = [
        'user_id',
        'project_offer_id',
        'amount',
        'note',
        'status',
        'paypal_order_id',
        'paypal_capture_id',
        'paypal_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paypal_payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offer()
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }
}
