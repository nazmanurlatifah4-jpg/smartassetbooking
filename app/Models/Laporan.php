<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use HasFactory, SoftDeletes;

    // Membatasi fillable untuk mencegah manipulasi ID atau Metadata internal secara langsung
    protected $fillable = [
        'admin_id',
        'judul',
        'periode',
        'keterangan',
        'status', // Status 'Draft' vs 'Final' untuk flow approval/validasi data
    ];

    /**
     * Relasi ke admin pembuat laporan untuk penanggung jawab data (audit trail)
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope ini disediakan agar filter laporan yang sudah siap dipublikasikan (Final) 
     * menjadi lebih deskriptif dan terpusat di satu tempat.
     */
    public function scopeFinal($query)
    {
        return $query->where('status', 'Final');
    }
}