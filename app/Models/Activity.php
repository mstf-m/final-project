<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

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
        'longitude',
        'image_url'
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
