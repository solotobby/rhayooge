<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BusinessExecutive extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'code',
        'invite_token',
        'magic_token',
        'magic_token_expires_at',
        'status',
        'default_commission_rate',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'invited_at',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'default_commission_rate' => 'integer',
            'invited_at' => 'datetime',
            'last_active_at' => 'datetime',
            'magic_token_expires_at' => 'datetime',
        ];
    }

    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtolower(trim($value));
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(BeCommission::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function totalSalesCount(): int
    {
        return $this->orders()->where('status', '!=', 'cancelled')->count();
    }

    public function totalSalesAmount(): int
    {
        return (int) $this->orders()->where('status', '!=', 'cancelled')->sum('total');
    }

    public function totalCommissionEarned(): int
    {
        return (int) $this->commissions()->where('status', '!=', 'cancelled')->sum('commission_amount');
    }

    public function pendingCommission(): int
    {
        return (int) $this->commissions()->whereIn('status', ['pending', 'approved'])->sum('commission_amount');
    }

    public function paidCommission(): int
    {
        return (int) $this->commissions()->where('status', 'paid')->sum('commission_amount');
    }

    public function inviteUrl(): string
    {
        return url('/executive/portal/'.$this->invite_token);
    }

    public function generateMagicLink(int $validHours = 72): string
    {
        $token = Str::random(48);
        $this->update([
            'magic_token' => $token,
            'magic_token_expires_at' => now()->addHours($validHours),
        ]);

        return url('/executive/portal/'.$token);
    }

    public function magicUrl(): string
    {
        if ($this->magic_token && $this->magic_token_expires_at && $this->magic_token_expires_at->isFuture()) {
            return url('/executive/portal/'.$this->magic_token);
        }

        return $this->inviteUrl();
    }

    public function productReferralUrl(Product $product): string
    {
        return url('/shop/'.$product->slug.'?ref='.$this->code);
    }

    public function storeReferralUrl(): string
    {
        return url('/shop?ref='.$this->code);
    }

    public static function generateUniqueCode(string $name): string
    {
        $base = Str::slug($name, '');
        if (empty($base)) {
            $base = 'exec';
        }

        $code = strtolower(substr($base, 0, 10));
        $attempt = $code;
        $counter = 1;

        while (self::query()->where('code', $attempt)->exists()) {
            $attempt = $code.$counter;
            $counter++;
        }

        return $attempt;
    }

    public static function generateInviteToken(): string
    {
        return Str::random(40);
    }
}
