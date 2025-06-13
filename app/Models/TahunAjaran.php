<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'tahun_awal',
        'tahun_akhir',
        'keterangan',
        'status',
    ];

    protected $attributes = [
        'status' => 0
    ];
}
