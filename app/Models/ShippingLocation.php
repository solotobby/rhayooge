<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingLocation extends Model
{
    protected $fillable = [
        'name',
        'fee',
        'estimated_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'fee' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function formattedFee(): string
    {
        return '₦' . number_format($this->fee);
    }
}
