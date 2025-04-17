<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityRequest extends Model
{
    protected $primaryKey = 'request_id';
    protected $table = 'requests';

    protected $fillable = [
        'activity_id',
        'user_id',
        'status',
        'latitude',
        'longitude'
    ];

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