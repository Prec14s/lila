<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_name', 'customer_phone', 'table_number', 'total',
        'payment_method', 'payment_category', 'payment_proof', 'payment_status', 'rejection_reason', 'order_status',
        'note', 'verified_by', 'verified_at', 'forwarded_to_kitchen_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'verified_at' => 'datetime',
            'forwarded_to_kitchen_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'WS-'.now()->format('Ymd').'-';
        $lastNumber = static::query()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('order_number');

        $sequence = $lastNumber ? ((int) Str::afterLast($lastNumber, '-')) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_verification' => 'Menunggu Verifikasi',
            'rejected' => 'Pembayaran Ditolak',
            'approved' => 'Disetujui',
            default => $this->payment_status,
        };
    }

    public function orderStatusLabel(): string
    {
        return match ($this->order_status) {
            'waiting' => 'Menunggu Diproses',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            default => $this->order_status,
        };
    }

    public function paymentStatusColor(): string
    {
        return match ($this->payment_status) {
            'waiting_payment' => 'bg-gray-100 text-gray-700',
            'waiting_verification' => 'bg-amber-100 text-amber-700',
            'rejected' => 'bg-red-100 text-red-700',
            'approved' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function orderStatusColor(): string
    {
        return match ($this->order_status) {
            'waiting' => 'bg-gray-100 text-gray-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function isCash(): bool
    {
        return $this->payment_category === 'cash';
    }

    public function paymentCategoryLabel(): string
    {
        return $this->isCash() ? 'Tunai' : 'Non-Tunai';
    }

    public function paymentCategoryColor(): string
    {
        return $this->isCash()
            ? 'bg-orange-100 text-orange-700'
            : 'bg-indigo-100 text-indigo-700';
    }

    public function paymentCategoryIcon(): string
    {
        return $this->isCash() ? '💵' : '📲';
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'cash' => 'Tunai di Kasir',
            default => '-',
        };
    }
}
