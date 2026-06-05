<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['ticket_id','user_id','message'];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
