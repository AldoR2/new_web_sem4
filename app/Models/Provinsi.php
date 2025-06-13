<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['id', 'name'];

    protected $table = 'provinsis';

    public function kotas()
    {
        return $this->hasMany(Kota::class, 'provinsi_id');
    }
}

