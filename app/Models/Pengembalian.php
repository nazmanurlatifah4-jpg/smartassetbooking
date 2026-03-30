<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $fillable = [
        'peminjaman_id', 'tanggal_pengembalian', 'kondisi_barang', 
        'foto', 'status_verifikasi', 'catatan_admin'
    ];

    public function peminjaman() {
        return $this->belongsTo(Peminjaman::class);
    }
}