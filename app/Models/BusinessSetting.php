<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name', 'address', 'wa_owner_number', 'wa_dapur_number',
    ];

    public static function instance(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'business_name' => 'Warkop Samalila',
            'wa_owner_number' => env('WA_OWNER_DEFAULT', '6281200000000'),
            'wa_dapur_number' => env('WA_DAPUR_DEFAULT', '6281200000001'),
        ]);
    }
}
