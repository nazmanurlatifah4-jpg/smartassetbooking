<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $fillable = [
        'peminjaman_id', 'pengembalian_id', 'jenis_denda', 'jumlah_hari', 
        'tarif_per_hari', 'total_denda', 'status_bayar', 'tanggal_lunas', 'catatan_admin'
    ];


    public function peminjaman() {
        return $this->belongsTo(Peminjaman::class);
    }
}