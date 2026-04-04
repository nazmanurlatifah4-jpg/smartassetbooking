<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────
        User::create([
            'nama'     => 'Budi Santoso',
            'email'    => 'admin@nexora.sch.id',
            'password' => Hash::make('password'),   // bcrypt otomatis
            'role'     => 'admin',
        ]);

        User::create([
            'nama'     => 'Nadin',
            'email'    => 'nadin@sch.id',
            'password' => Hash::make('password'),   // bcrypt otomatis
            'role'     => 'admin',
        ]);


        // ── Peminjam (Siswa) ──────────────────────────────────
        $peminjam = [
            ['nama' => 'Ahmad Fauzi', 'kelas' => 'XII TKJ 1', 'jurusan' => 'Teknik Komputer Jaringan'],
            ['nama' => 'Siti Nurhaliza', 'kelas' => 'XI MM 2',   'jurusan' => 'Multimedia'],
            ['nama' => 'Rizky Pratama', 'kelas' => 'X AK 1',    'jurusan' => 'Akuntansi'],
            ['nama' => 'Maya Anggraini', 'kelas' => 'XII TB 1',  'jurusan' => 'Tata Busana'],
            ['nama' => 'Dani Kurniawan', 'kelas' => 'XI TO 2',   'jurusan' => 'Teknik Otomotif'],
        ];

        foreach ($peminjam as $p) {
            User::create([
                'nama'     => $p['nama'],
                'password' => Hash::make('password'),
                'role'     => 'peminjam',
                'kelas'    => $p['kelas'],
                'jurusan'  => $p['jurusan'],
            ]);
        }
    }
}
