<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use SoftDeletes;

    protected $table = 'peminjamans';

    protected $fillable = [
        'user_id', 'aset_id', 'tanggal_pengajuan', 'tanggal_disetujui', 
        'tanggal_kembali', 'keperluan', 'status', 'catatan'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function aset() {
        return $this->belongsTo(Aset::class);
    }

    public function pengembalian() {
        return $this->hasOne(Pengembalian::class);
    }

    public function denda() {
        return $this->hasOne(Denda::class);
    }

    public function isTerlambat() {
        return $this->tanggal_kembali < now() && $this->status !== 'Selesai';
    }
}