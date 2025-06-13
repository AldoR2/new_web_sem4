<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class Dosen extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jenis_kelamin',
        'agama',
        'tempat_lahir',
        'tgl_lahir',
        'email',
        'no_telp',
        'alamat',
        'prodi_id',
        'foto',
        'provinsi_id',
        'kota_id',
        'kecamatan_id',
        'kelurahan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }
    public function kota()
    {
        return $this->belongsTo(Kota::class, 'kota_id', 'id');
    }
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($dosen) {
            // Hapus foto jika ada
            if ($dosen->foto && Storage::disk('public')->exists($dosen->foto)) {
                Storage::disk('public')->delete($dosen->foto);
            }

            // Jika kamu mau sekalian hapus user terkait:
            if ($dosen->user) {
                $dosen->user->delete();
            }
        });
    }
}
