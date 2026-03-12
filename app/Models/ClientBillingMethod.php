<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBillingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method_type',
        'label',
        'last_four',
        'is_default',
    ];

    protected $appends = [
        'display_label',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->method_type === 'PayPal') {
            return filled($this->label)
                ? 'PayPal · ' . $this->label
                : 'PayPal';
        }

        if (filled($this->last_four)) {
            return $this->method_type . ' · •••• ' . $this->last_four;
        }

        return filled($this->label)
            ? $this->method_type . ' · ' . $this->label
            : $this->method_type;
    }
}
