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
        // Hitung semua aset yang sedang diproses atau dipinjam
        // Karena 'jumlah' sekarang sudah dicatat di database
        $dipinjam = $this->peminjaman()
            ->whereIn('status', ['Menunggu', 'Disetujui', 'Terlambat'])
            ->sum('jumlah');
        
        // Stok tersedia adalah stok total DB dikurangi yang sedang aktif dipinjam
        return max(0, $this->stok - $dipinjam);
    }

    public function peminjaman(): HasMany
{
    // Pastikan 'aset_id' adalah nama kolom foreign key di tabel peminjaman kamu
    return $this->hasMany(Peminjaman::class, 'aset_id', 'id');
}
    
}