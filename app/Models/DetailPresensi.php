<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DetailPresensi extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'presensi_id',
        'mahasiswa_id',
        'waktu_presensi',
        'status',
        'alasan',
        'bukti'
    ];

        public $timestamps = false;


        public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id', 'id');
    }

        public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id', 'id');
    }
}
