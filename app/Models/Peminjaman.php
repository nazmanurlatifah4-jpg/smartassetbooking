<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Peminjaman extends Model
{
    use SoftDeletes;

    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id', 
        'aset_id', 
        'jumlah',
        'admin_id', // Pastikan kolom ini ada di database jika pakai relasi admin
        'tanggal_pinjam',    
        'tanggal_pengajuan', 
        'tanggal_disetujui', 
        'tanggal_kembali', 
        'keperluan', 
        'status', 
        'catatan'
    ];

    // SATU BLOK CASTS SAJA UNTUK SEMUA TANGGAL
    protected $casts = [
        'tanggal_pinjam'    => 'datetime',
        'tanggal_pengajuan' => 'datetime',
        'tanggal_disetujui' => 'datetime',
        'tanggal_kembali'   => 'datetime',
    ];

    // --- Relasi ---

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aset() {
        return $this->belongsTo(Aset::class, 'aset_id');
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function verifikasi() {
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
        if (!$this->tanggal_kembali) return false;
        /** @var Carbon $tgl */
        $tgl = $this->tanggal_kembali;
        return $tgl->isPast();
    }
    
    // Hitung berapa hari terlambat
    public function getHariTerlambatAttribute() {
        if (!$this->isTerlambat()) return 0;
        /** @var Carbon $tgl */
        $tgl = $this->tanggal_kembali;
        return $tgl->diffInDays(Carbon::now());
    }
}