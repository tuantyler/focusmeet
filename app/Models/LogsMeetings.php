<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsMeetings extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'logs_meetings';

    /**
     * Fillable fields for a Profile.
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        "meeting_id",
        "log_description"
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
