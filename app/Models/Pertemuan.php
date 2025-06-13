<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pertemuan extends Model
{
    use HasFactory, Notifiable;

        protected $fillable = [
        'pertemuan_ke',
        'prodi_id',
        'semester',
        'matkul_id',
        'tahun_ajaran_id',
        'status',
    ];

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'pertemuan_id', 'id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function tahun()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

    public function matkul()
    {
        return $this->belongsTo(Matkul::class, 'matkul_id', 'id');
    }
}
