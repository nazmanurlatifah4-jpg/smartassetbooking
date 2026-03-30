<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    use HasFactory;

    // Kolom yang dapat diisi berdasarkan migration verifikasis
    protected $fillable = [
        'peminjaman_id',
        'admin_id',
        'status',
        'catatan',
        'tanggal',
    ];

    /**
     * Relasi ke Peminjaman
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * Relasi ke Admin (User) yang melakukan verifikasi
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}