<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Peminjaman;
use App\Models\Notifikasi; // Tambahkan ini di atas
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // === FUNGSI YANG BARU (UNTUK SETUJUI PINJAMAN) ===
    public function setujuiPeminjaman($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            // Update status jadi 'Disetujui' agar muncul di statistik dashboard
            $peminjaman->status = 'Disetujui'; 
            $peminjaman->save();

            // Kirim notif ke peminjam
            Notifikasi::create([
                'user_id'    => $peminjaman->user_id,
                'judul'      => 'Peminjaman Disetujui',
                'pesan'      => 'Aset ' . $peminjaman->aset->nama_aset . ' sudah disetujui dan boleh diambil.',
                'tanda_baca' => 'Belum Dibaca'
            ]);

            return redirect()->back()->with('success', 'Peminjaman berhasil disetujui!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // === FUNGSI LAMA KAMU (UNTUK DENDA) ===
    public function konfirmasiLunas($idDenda)
    {
        $denda = Denda::findOrFail($idDenda);

        DB::transaction(function () use ($denda) {
            $denda->update([
                'status_bayar' => 'Lunas',
                'catatan_admin' => 'Denda dibayar tunai dan dikonfirmasi admin.'
            ]);

            $denda->peminjaman->update(['status' => 'Selesai']);
            
            if($denda->pengembalian) {
                $denda->pengembalian->update(['status_verifikasi' => 'Diterima']);
            }
        });

        return back()->with('success', 'Denda Lunas! Status peminjaman kini SELESAI.');
    }
}