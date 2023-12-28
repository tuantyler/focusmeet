<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\LogsMeetings;

class Meet extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'meetings';

    protected $primaryKey = 'meeting_id';
    protected $keyType = 'string';

    /**
     * Fillable fields for a Profile.
     *
     * @var array
     */
    protected $fillable = [
        'meeting_id',
        'meeting_name',
        'user_id',
        'start_time',
        'end_time',
        'all_days',
        'allow_freely_join',
        'enable_member_focus_view',
        'enable_member_list_view',
        'repeat_pattern'
    ];

    public function user_id()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function meetings_log()
    {
        return $this->hasMany(LogsMeetings::class, 'meeting_id');
    }
}
