<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Presensi extends Model
{
    use HasFactory, Notifiable;

        protected $fillable = [
        'presensi_id',
        'pertemuan_id',
        'tgl_presensi',
        'jam_awal',
        'jam_akhir',
        'dosen_id',
        'ruangan_id',
        'link_zoom'
    ];

    public function detailPresensi()
    {
        return $this->hasMany(DetailPresensi::class, 'presensi_id', 'id');
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id', 'id');
    }

    // public function prodi()
    // {
    //     return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    // }

    // public function tahun()
    // {
    //     return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    // }

    // public function matkul()
    // {
    //     return $this->belongsTo(Matkul::class, 'matkul_id', 'id');
    // }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id', 'id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id', 'id');
    }
}
