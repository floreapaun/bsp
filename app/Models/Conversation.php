<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'post_id',
        'user_one_id',
        'user_two_id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }
}
