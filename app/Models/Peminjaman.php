<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Verifikasi;
use App\Models\Pengembalian;
use App\Models\Denda;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use SoftDeletes;

    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id', 
        'aset_id', 
        'tanggal_pinjam',    
        'tanggal_pengajuan', 
        'tanggal_disetujui', 
        'tanggal_kembali', 
        'keperluan', 
        'status', 
        'catatan'
    ];

    protected $casts = [
        'tanggal_pinjam'    => 'date',
        'tanggal_pengajuan' => 'date',
        'tanggal_disetujui' => 'date',
        'tanggal_kembali'   => 'date',
    ];

    // --- Relasi ---

    public function admin()
    {
    return $this->belongsTo(User::class, 'admin_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function aset() {
        return $this->belongsTo(Aset::class);
    }

    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class);
    }

    public function pengembalian() {
        return $this->hasOne(Pengembalian::class);
    }

    public function denda() {
        return $this->hasOne(Denda::class);
    }

    // --- Helpers ---

    public function isTerlambat() {
        // Cek apakah sudah melewati tanggal kembali dan belum selesai
        if (!$this->tanggal_kembali || $this->status === 'Selesai') return false;
        return $this->tanggal_kembali->isPast();
    }
    
    // Hitung berapa hari terlambat
    public function getHariTerlambatAttribute() {
        if (!$this->isTerlambat()) return 0;
        return $this->tanggal_kembali->diffInDays(now());
    }
}
