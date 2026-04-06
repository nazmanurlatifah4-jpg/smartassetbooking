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

    /**
     * Menghitung stok riil yang tersedia dengan mempertimbangkan antrean peminjaman.
     * Status 'Menunggu' dan 'Disetujui' dianggap mengurangi stok untuk mencegah overbooking.
     */
    public function stokTersedia()
    {
        $dipinjam = $this->peminjaman()
            ->whereIn('status', ['Menunggu', 'Disetujui', 'Terlambat'])
            ->sum('jumlah');
        
        return max(0, $this->stok - $dipinjam);
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'aset_id', 'id');
    }
}