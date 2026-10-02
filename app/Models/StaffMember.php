<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    public const TYPES = ['sister', 'technician'];

    protected $fillable = [
        'type',
        'name',
        'mobile',
        'email',
        'address',
    ];

    public static function names(string $type): array
    {
        return static::query()
            ->where('type', $type)
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }
}