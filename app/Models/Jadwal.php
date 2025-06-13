<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Jadwal extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'jam',
        'durasi',
        'hari',
        'dosen_id',
        'prodi_id',
        'matkul_id',
        'ruangan_id',
        'tahun_ajaran_id',
        'semester'
    ];

        public function detailJadwal()
    {
        return $this->hasMany(DetailJadwal::class, 'jadwal_id', 'id');
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

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id', 'id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'id');
    }
}
