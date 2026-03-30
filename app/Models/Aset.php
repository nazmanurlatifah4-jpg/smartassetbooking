<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_aset', 'nama_aset', 'kategori', 'kondisi', 'stok', 'lokasi', 'deskripsi', 'foto'
    ];
}