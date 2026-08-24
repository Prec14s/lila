<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'photo', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'price' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : 'https://placehold.co/400x300/6F4E37/F5EDE3?text='.urlencode($this->name);
    }
}
