<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Notifikasi;

/**
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Notifikasi[] $notifikasi
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    /**
     * Kolom yang bisa diisi (Mass Assignment)
     * Sesuaikan dengan migration 2014_10_12_000000_create_users_table.php
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'kelas',
        'jurusan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * RELASI: User memiliki banyak Notifikasi
     * Ini untuk memperbaiki error "Call to undefined method notifikasi()"
     */
    public function notifikasi(): HasMany
    {
        // 'user_id' adalah foreign key di tabel notifikasis
        return $this->hasMany(Notifikasi::class, 'user_id', 'id');
    }

    /**
     * RELASI: User (Peminjam) memiliki banyak data Peminjaman
     */
    public function peminjamans(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'user_id', 'id');
    }
}