<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;


class Mahasiswa extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'user_id',
        'nim',
        'rfid',
        'nama',
        'jenis_kelamin',
        'agama',
        'tempat_lahir',
        'tgl_lahir',
        'email',
        'no_telp',
        'alamat',
        'prodi_id',
        'tahun_masuk',
        'tahun_ajaran_id',
        'semester',
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

    public function tahun()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
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
        static::deleting(function ($mahasiswa) {
            // Hapus foto jika ada
            if ($mahasiswa->foto && Storage::disk('public')->exists($mahasiswa->foto)) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }

            // Jika kamu mau sekalian hapus user terkait:
            if ($mahasiswa->user) {
                $mahasiswa->user->delete();
            }
        });
    }

    // Method ini digunakan untuk mengambil email untuk verifikasi
    public function getEmailForVerification()
    {
        return $this->email;  // Atau jika kamu ingin custom, bisa menambahkan logika lainnya
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
