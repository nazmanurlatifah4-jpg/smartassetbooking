<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    // Ini wajib karena migration kamu namanya 'notifikasis'
    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id', 
        'judul', 
        'pesan', 
        'tanda_baca', 
        'tanggal_kirim'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Kirim notifikasi ke user tertentu
     */
    public static function kirim($userId, $judul, $pesan)
    {
        return self::create([
            'user_id'      => $userId,
            'judul'        => $judul,
            'pesan'        => $pesan,
            'tanda_baca'   => 'Belum Dibaca',
            'tanggal_kirim'=> now(),
        ]);
    }
}