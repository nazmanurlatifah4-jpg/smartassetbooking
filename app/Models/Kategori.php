<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel ditentukan eksplisit untuk kepastian (defaulting)
    protected $table = 'kategoris';

    // Daftar isian massal dibatasi hanya untuk data inti kategori agar menghindari manipulasi field internal
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Relasi ke model Aset menggunakan 'nama' sebagai foreign key karena tabel 'asets' saat ini
     * menyimpan nilai kategori dalam bentuk string (bukan ID), memudahkan pembacaan langsung di DB.
     */
    public function asets(): HasMany
    {
        return $this->hasMany(Aset::class, 'kategori', 'nama');
    }
}
