<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isDapur(): bool
    {
        return $this->role === 'dapur';
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            'owner' => 'Owner',
            'dapur' => 'Staf Dapur',
            default => ucfirst((string) $this->role),
        };
    }
}
