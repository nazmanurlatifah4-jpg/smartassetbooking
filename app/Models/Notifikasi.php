<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    // Kolom sesuai migration notifikasis
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tanda_baca',
        'tanggal_kirim',
    ];

    /**
     * Relasi ke User yang menerima notifikasi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}