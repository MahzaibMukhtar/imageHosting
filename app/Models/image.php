<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = 'images';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'date',
        'time',
        'extension',
        'path',
        'status',

    ];
}
