<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionSetting extends Model
{
    protected $fillable = [
        'trial_started_at',
        'trial_duration_days',
        'trial_duration_minutes',
        'paid_until',
        'bank_details',
        'upi_id',
        'payment_instructions',
        'qr_code_path',
    ];

    protected $casts = [
        'trial_started_at' => 'datetime',
        'trial_duration_days' => 'integer',
        'trial_duration_minutes' => 'integer',
        'paid_until' => 'datetime',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'trial_started_at' => now(),
                'trial_duration_days' => 15,
                'bank_details' => "HDFC Bank (Primary)\nAccount holder: NBUNIFY PRIVATE LIMITED\nAccount No: 502000117622680\nIFSC: HDFC0002504\nMICR: -\nBranch: THANE\nUPI: 7710020126@hdfc",
                'upi_id' => '7710020126@hdfc',
            ]
        );
    }
}