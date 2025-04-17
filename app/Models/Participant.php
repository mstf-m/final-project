<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $primaryKey = 'participant_id';
    protected $table = 'participants';

    protected $fillable = ['activity_id', 'user_id', 'status'];

    protected $casts = [
        'status' => 'string'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}