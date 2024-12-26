<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publications extends Model
{
    protected $fillable = [
        'date',
        'text',
        'hashtags',
        'status',
        'user_id',
        'images',
        'title',
    ];

    protected $casts = [
        'images' => 'array', 
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'publication_id');
    }

    public function likers()
    {
        return $this->belongsToMany(
            User::class,           
            'publication_user_likes', 
            'publication_id',         
            'user_id'                    
        );
    }
    
}
