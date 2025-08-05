<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'nama_user',
        'tanggal',
        'jam',
        'mata_kuliah',
        'presensi_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'presensi_id' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
