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
        'jenis',
        'prodi_id',
        'semester',
        'matkul_id',
        'tahun_ajaran_id',
        'status',
    ];

    protected $casts = [
        'pertemuan_ke' => 'integer',
        'prodi_id' => 'integer',
        'semester' => 'integer',
        'matkul_id' => 'integer',
        'tahun_ajaran_id' => 'integer',
    ];

    public function presensi()
    {
        return $this->hasOne(Presensi::class, 'pertemuan_id', 'id');
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
