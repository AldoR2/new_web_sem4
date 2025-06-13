<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpToken extends Model
{
    protected $primaryKey = 'email';
    public $incrementing = false; // karena bukan auto-increment
    protected $keyType = 'string'; // karena email biasanya string
    protected $fillable = ['email', 'token', 'expired_at'];
    public $timestamps = false;
}
