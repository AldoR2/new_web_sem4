<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Notifications\Notifiable;


class Matkul extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'kode_matkul',
        'nama_matkul',
        'tahun_ajaran_id',
        'semester',
        'durasi_matkul',
        'prodi_id',
    ];

    protected $casts = [
        'tahun_ajaran_id' => 'integer',
        'semester' => 'integer',
        'prodi_id' => 'integer',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function pertemuan()
    {
        return $this->hasMany(Pertemuan::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

}
