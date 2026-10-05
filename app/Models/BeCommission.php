<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeCommission extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_executive_id',
        'order_id',
        'product_id',
        'product_name',
        'sale_amount',
        'commission_amount',
        'commission_rate',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'sale_amount' => 'integer',
            'commission_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function executive(): BelongsTo
    {
        return $this->belongsTo(BusinessExecutive::class, 'business_executive_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function formattedCommission(): string
    {
        return Product::naira($this->commission_amount);
    }

    public function formattedSale(): string
    {
        return Product::naira($this->sale_amount);
    }
}
