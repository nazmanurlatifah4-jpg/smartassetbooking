<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aset;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $asets = [
            ['kode_aset' => 'AST-001', 'nama_aset' => 'Laptop Lenovo ThinkPad',   'kategori' => 'Elektronik',   'kondisi' => 'Baik',         'stok' => 5,  'lokasi' => 'Lab Komputer 1'],
            ['kode_aset' => 'AST-002', 'nama_aset' => 'Proyektor Epson EB-X41',   'kategori' => 'Elektronik',   'kondisi' => 'Baik',         'stok' => 3,  'lokasi' => 'Gudang AV'],
            ['kode_aset' => 'AST-003', 'nama_aset' => 'Kamera DSLR Canon 1300D',  'kategori' => 'Elektronik',   'kondisi' => 'Baik',         'stok' => 2,  'lokasi' => 'Lab Multimedia'],
            ['kode_aset' => 'AST-004', 'nama_aset' => 'Tripod Kamera',            'kategori' => 'Perlengkapan', 'kondisi' => 'Baik',         'stok' => 4,  'lokasi' => 'Lab Multimedia'],
            ['kode_aset' => 'AST-005', 'nama_aset' => 'Mikrofon Condenser',       'kategori' => 'Elektronik',   'kondisi' => 'Rusak Ringan', 'stok' => 2,  'lokasi' => 'Studio'],
            ['kode_aset' => 'AST-006', 'nama_aset' => 'Printer HP LaserJet',      'kategori' => 'Elektronik',   'kondisi' => 'Baik',         'stok' => 2,  'lokasi' => 'Ruang TU'],
            ['kode_aset' => 'AST-007', 'nama_aset' => 'Meja Lipat',               'kategori' => 'Furnitur',     'kondisi' => 'Baik',         'stok' => 10, 'lokasi' => 'Gudang'],
            ['kode_aset' => 'AST-008', 'nama_aset' => 'Kursi Plastik',            'kategori' => 'Furnitur',     'kondisi' => 'Baik',         'stok' => 20, 'lokasi' => 'Gudang'],
            ['kode_aset' => 'AST-009', 'nama_aset' => 'Toolkit Elektronik',       'kategori' => 'Alat',         'kondisi' => 'Baik',         'stok' => 5,  'lokasi' => 'Lab TKJ'],
            ['kode_aset' => 'AST-010', 'nama_aset' => 'Papan Tulis Portable',     'kategori' => 'Perlengkapan', 'kondisi' => 'Baik',         'stok' => 3,  'lokasi' => 'Gudang'],
        ];

        foreach ($asets as $aset) {
            Aset::create($aset);
        }
    }
}
