<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientVerify extends Model
{
    use HasFactory;
    protected $table='token';
    protected $fillable=[
        'user_id',
        'remember_me',
    ];
}
