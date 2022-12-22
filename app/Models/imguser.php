<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imguser extends Model
{
    use HasFactory;
    protected $table = 'image_user';
    public $incrementing = true;
    protected $fillable = [
        'user_id',
        'image_id',
    ];
}
