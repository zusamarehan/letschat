<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConnection extends Model
{
    use HasFactory;

    public function connections()
    {
        return $this->hasMany(UserConnection::class, 'connection_id', 'user_id');
    }
}
