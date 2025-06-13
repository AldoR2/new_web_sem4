<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DetailJadwal extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'jadwal_id',
        'mahasiswa_id'
    ];

        public $timestamps = false;

        public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
    }
}
