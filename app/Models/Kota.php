<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kota extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = ['id', 'provinsi_id', 'name'];

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class);
    }
    public function provinsi()
{
    return $this->belongsTo(Provinsi::class, 'provinsi_id');
}

}

