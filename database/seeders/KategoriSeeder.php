<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'Elektronik', 'deskripsi' => 'Aset berupa perangkat elektronik kantor dan sekolah.'],
            ['nama' => 'Fotografi', 'deskripsi' => 'Perangkat pendukung visual dan kamera.'],
            ['nama' => 'Audio', 'deskripsi' => 'Perlengkapan suara dan rekaman'],
        ];

        foreach ($kategoris as $k) {
            \App\Models\Kategori::updateOrCreate(['nama' => $k['nama']], $k);
        }
    }
}
