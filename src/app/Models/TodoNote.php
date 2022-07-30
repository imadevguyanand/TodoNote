<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodoNote extends Model 
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'content', 
        'completed_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\TodoNote');
    }
}
