<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aset extends Model
{
    use SoftDeletes;

    protected $fillable = [
    'kode_aset', 'nama_aset', 'kategori', 'kondisi', 'stok', 'lokasi', 'deskripsi', 'foto'
    ];

    public function peminjamans(): HasMany
{
    // Pastikan 'aset_id' adalah nama kolom foreign key di tabel peminjaman kamu
    return $this->hasMany(Peminjaman::class, 'aset_id', 'id');
}
    
}