<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Admin extends Model
{
    protected $fillable=[
        'nombres',
        'apellidos',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'admin_id', 'id');
    }
    
}
