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

    public function stokTersedia()
    {
        // Menghitung berapa banyak aset ini yang statusnya sedang 'Disetujui' (sedang dipinjam)
        $dipinjam = $this->peminjaman()->where('status', 'Disetujui')->count();
        
        // Stok tersedia adalah stok awal dikurangi yang sedang keluar
        return $this->stok - $dipinjam;
    }

    public function peminjaman(): HasMany
{
    // Pastikan 'aset_id' adalah nama kolom foreign key di tabel peminjaman kamu
    return $this->hasMany(Peminjaman::class, 'aset_id', 'id');
}
    
}