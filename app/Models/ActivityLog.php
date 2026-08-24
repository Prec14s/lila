<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, string $description, ?User $user = null, ?string $fallbackName = null): self
    {
        $targetUser = $user ?? auth()->user();

        return self::create([
            'user_id' => $targetUser?->id,
            'user_name' => $targetUser?->name ?? $fallbackName ?? 'Pelanggan',
            'role' => $targetUser?->role ?? 'customer',
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function roleBadgeColor(): string
    {
        return match ($this->role) {
            'superadmin' => 'bg-purple-100 text-purple-700 border-purple-200',
            'owner' => 'bg-coffee-800 text-white border-coffee-900',
            'dapur' => 'bg-blue-100 text-blue-700 border-blue-200',
            default => 'bg-amber-100 text-amber-800 border-amber-200',
        };
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            'owner' => 'Owner',
            'dapur' => 'Staf Dapur',
            default => 'Pelanggan',
        };
    }
}
