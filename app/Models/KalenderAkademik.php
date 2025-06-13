<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    //
    protected $fillable = ['judul', 'deskripsi', 'tanggal_mulai', 'tanggal_selesai','status'];

}
