<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $table = 'kelurahans'; // pastikan tabelnya

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id'); 
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }
}
