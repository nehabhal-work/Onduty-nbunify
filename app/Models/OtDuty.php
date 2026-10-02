<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtDuty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sister_name',
        'technician_name',
        'date_time',
        'section',
        'ot_no',
        'shift',
        'department',
        'unit_no',
        'surgery',
        'remarks',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'ot_no' => 'integer',
        'unit_no' => 'integer',
    ];
}
