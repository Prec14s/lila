<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'label', 'qris_image', 'bank_name', 'account_number', 'account_holder', 'instruction', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function isCash(): bool
    {
        return $this->type === 'cash';
    }

    public function displayLabel(): string
    {
        return match ($this->type) {
            'qris' => $this->label ?: 'QRIS',
            'bank_transfer' => $this->label ?: ('Transfer '.$this->bank_name),
            'cash' => $this->label ?: 'Tunai / Bayar di Kasir',
            default => $this->label ?? '-',
        };
    }

    public function icon(): string
    {
        return match ($this->type) {
            'qris' => '📱',
            'bank_transfer' => '🏦',
            'cash' => '💵',
            default => '💳',
        };
    }
}
