<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepeatPattern extends Model
{
    use HasFactory;

    /**
     * Fillable fields for a Profile.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id',
        'repeat_type',
        'day_of_week'
    ];
}
