<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = ['confirmed', 'preparing', 'dispatched', 'delivered', 'cancelled'];

    protected $fillable = [
        'user_id', 'business_executive_id', 'be_code', 'name', 'email', 'phone', 'address', 'city',
        'shipping_location_id', 'shipping_location_name',
        'payment_method', 'payment_reference', 'payment_status', 'paid_at',
        'subtotal', 'delivery', 'total', 'status',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function shippingLocation(): BelongsTo
    {
        return $this->belongsTo(ShippingLocation::class, 'shipping_location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function businessExecutive(): BelongsTo
    {
        return $this->belongsTo(BusinessExecutive::class, 'business_executive_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(BeCommission::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function formattedTotal(): string
    {
        return Product::naira((int) $this->total);
    }
}
