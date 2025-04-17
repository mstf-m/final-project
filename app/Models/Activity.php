<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $primaryKey = 'activity_id';
    protected $table = 'activities';

    protected $fillable = [
        'creator_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'max_participants',
        'category_id',
        'latitude',
        'longitude'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'activity_id');
    }

    public function requests()
    {
        return $this->hasMany(ActivityRequest::class, 'activity_id');
    }
}