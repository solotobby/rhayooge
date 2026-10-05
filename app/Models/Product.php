<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug', 'name', 'category', 'price', 'original_price', 'sizes', 'size_mode', 'quantity',
        'status', 'size_inventory',
        'commission_type', 'commission_rate', 'featured', 'newest', 'description', 'image', 'images',
    ];

    protected function casts(): array
    {
        return [
            'sizes' => 'array',
            'size_inventory' => 'array',
            'images' => 'array',
            'featured' => 'boolean',
            'newest' => 'boolean',
            'price' => 'integer',
            'original_price' => 'integer',
            'quantity' => 'integer',
            'size_mode' => 'string',
            'status' => 'string',
            'commission_type' => 'string',
            'commission_rate' => 'integer',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function isPublished(): bool
    {
        return ($this->status ?? 'published') === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function stockForSize(string $size): int
    {
        if (is_array($this->size_inventory) && ! empty($this->size_inventory)) {
            return (int) ($this->size_inventory[$size] ?? 0);
        }

        if (is_array($this->sizes) && ! in_array($size, $this->sizes, true)) {
            return 0;
        }

        return $this->quantity;
    }

    public function isSizeInStock(string $size): bool
    {
        return $this->stockForSize($size) > 0;
    }

    public function galleryImages(): array
    {
        $list = [];

        if (! empty($this->images) && is_array($this->images)) {
            foreach ($this->images as $img) {
                $cleaned = trim((string) $img);
                if ($cleaned !== '') {
                    $list[] = self::normalizeImageUrl($cleaned);
                }
            }
        }

        if (empty($list) && ! empty($this->image)) {
            $list[] = self::normalizeImageUrl($this->image);
        }

        if (empty($list)) {
            $list[] = asset('assets/brand-guide.png');
        }

        return array_values(array_unique($list));
    }

    public function primaryImage(): string
    {
        $gallery = $this->galleryImages();

        return $gallery[0] ?? asset('assets/brand-guide.png');
    }

    public static function normalizeImageUrl(string $url): string
    {
        $url = trim($url);

        // Convert Google Drive share link to direct image view if applicable
        if (preg_match('/drive\.google\.com\/(?:file\/d\/|open\?id=)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://lh3.googleusercontent.com/d/'.$matches[1];
        }

        return $url;
    }

    public function formattedPrice(): string
    {
        return self::naira($this->price);
    }

    public function hasDiscount(): bool
    {
        return ! empty($this->original_price) && $this->original_price > $this->price;
    }

    public function formattedOriginalPrice(): ?string
    {
        return $this->original_price ? self::naira($this->original_price) : null;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    public function inStock(): bool
    {
        return $this->quantity > 0;
    }

    public function calculateCommissionAmount(?int $salePrice = null, int $fallbackRate = 10): int
    {
        $price = $salePrice ?? $this->price;
        $type = $this->commission_type ?? 'percent';
        $rate = $this->commission_rate ?? $fallbackRate;

        if ($type === 'fixed') {
            return (int) min($rate, $price);
        }

        return (int) round(($price * $rate) / 100);
    }

    public function executiveCommissionAmount(): int
    {
        return $this->calculateCommissionAmount();
    }

    public function formattedCommissionLabel(?int $salePrice = null, int $fallbackRate = 10): string
    {
        $amount = $this->calculateCommissionAmount($salePrice, $fallbackRate);
        $type = $this->commission_type ?? 'percent';
        $rate = $this->commission_rate ?? $fallbackRate;

        if ($type === 'fixed') {
            return self::naira($amount).' fixed';
        }

        return self::naira($amount)." ({$rate}%)";
    }

    public static function naira(int $amount): string
    {
        return '₦'.number_format($amount);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
