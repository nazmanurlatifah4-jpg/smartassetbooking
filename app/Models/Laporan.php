<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     * manajemen_id dan catatan_manajemen dihapus karena role manajemen tidak lagi digunakan.
     */
    protected $fillable = [
        'admin_id',
        'judul',
        'periode',
        'keterangan',
        'status', // Isinya nanti antara: 'Draft' atau 'Final'
    ];

    /**
     * Relasi: Laporan dibuat oleh seorang Admin.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope untuk mempermudah filter laporan yang sudah Final di Controller.
     */
    public function scopeFinal($query)
    {
        return $query->where('status', 'Final');
    }
}