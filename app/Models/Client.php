<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table = 'users';
    //protected $hidden = ['pivot'];
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'password',
        'img',

    ];
    public function image()
    {
        return $this->belongsToMany(image::class, 'image_user', 'user_id', 'image_id')
        ->withTimestamps();
    }
    public function client_verify()
    {
       return $this->hasMany(ClientVerify::class);
    }
}
