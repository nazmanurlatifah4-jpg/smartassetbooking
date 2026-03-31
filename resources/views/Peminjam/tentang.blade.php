@extends('layouts.peminjam')
@section('title', 'Tentang')
@section('page-subtitle', 'Panduan Penggunaan')

@push('styles')
<style>
    .guide-step {
        border-left: 4px solid #3b82f6; padding: 14px 16px;
        margin-bottom: 14px; background: #f8fafc; border-radius: 0 8px 8px 0;
    }
    .guide-step h4 { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; }
    .guide-step p  { font-size: 13px; color: #475569; line-height: 1.6; }
    .guide-step ul { font-size: 13px; color: #475569; margin-top: 8px; padding-left: 20px; line-height: 1.8; list-style: disc; }
    .step-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; background: #3b82f6; color: white;
        border-radius: 50%; font-size: 13px; font-weight: 800; flex-shrink: 0;
    }
    .faq-item { border: 1.5px solid #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 10px; }
    .faq-q {
        padding: 14px 18px; font-size: 14px; font-weight: 600; color: #1e293b;
        display: flex; justify-content: space-between; align-items: center;
        cursor: pointer; transition: background 0.2s;
    }
    .faq-q:hover { background: #f8fafc; }
    .faq-q i { color: #94a3b8; transition: transform 0.3s; font-size: 12px; }
    .faq-q.open i { transform: rotate(180deg); }
    .faq-a { display: none; padding: 0 18px 14px; font-size: 13px; color: #475569; line-height: 1.6; }
    .faq-a.open { display: block; }
</style>
@endpush

@section('content')

<div class="text-center mb-6 sm:mb-8">
    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#1e293b] mb-2">Panduan Penggunaan</h2>
    <p class="text-xs sm:text-sm text-[#64748b]">Petunjuk lengkap menggunakan Smart Asset Booking</p>
</div>

{{-- ALUR PEMINJAMAN --}}
<div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-6">
    <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-hand-holding text-blue-500"></i> Alur Peminjaman Aset
    </h3>
    <div class="guide-step">
        <h4><span class="step-num">1</span> Pilih Aset di Data Aset</h4>
        <p>Buka menu <strong>Data Aset</strong>. Di setiap kartu aset tersedia 3 pilihan:</p>
        <ul>
            <li><strong class="text-blue-600">Sekarang</strong> — Pinjam 1 aset langsung (redirect ke form peminjaman)</li>
            <li><strong class="text-green-600">+Keranjang</strong> — Tambah ke keranjang untuk meminjam beberapa aset sekaligus</li>
            <li><strong class="text-gray-600">Detail</strong> — Lihat informasi lengkap aset</li>
        </ul>
    </div>
    <div class="guide-step">
        <h4><span class="step-num">2</span> Proses di Keranjang</h4>
        <p>Jika memilih <strong>+Keranjang</strong>, aset akan masuk ke keranjang. Klik icon <i class="fas fa-shopping-cart text-green-600"></i> di navbar untuk masuk ke halaman <strong>Transaksi Peminjaman</strong>.</p>
    </div>
    <div class="guide-step">
        <h4><span class="step-num">3</span> Isi Form Peminjaman</h4>
        <p>Di halaman Transaksi Peminjaman:</p>
        <ul>
            <li>Data peminjam otomatis terisi</li>
            <li>Atur jumlah aset yang dipinjam</li>
            <li>Tentukan tanggal pinjam dan rencana kembali (maksimal 7 hari)</li>
            <li>Isi tujuan peminjaman</li>
            <li>Klik <strong class="text-green-600">"Tambah Aset Lagi"</strong> jika ingin menambah aset lain (kembali ke Data Aset)</li>
            <li>Klik <strong class="text-blue-600">"Kirim ke Admin"</strong> jika sudah selesai</li>
        </ul>
    </div>
    <div class="guide-step">
        <h4><span class="step-num">4</span> Tunggu Persetujuan Admin</h4>
        <p>Setelah dikirim, status akan <strong class="text-yellow-600">Pending</strong>. Admin akan memproses dalam 1–2 hari kerja.</p>
    </div>
    <div class="guide-step">
        <h4><span class="step-num">5</span> Ambil Barang (Jika Disetujui)</h4>
        <p>Jika status berubah menjadi <strong class="text-green-600">Disetujui</strong>:</p>
        <ul>
            <li>Datang ke petugas dengan membawa <strong>bukti persetujuan</strong> (screenshot dari menu Riwayat)</li>
            <li>Petugas akan memberikan aset yang dipinjam</li>
            <li>Pastikan mengecek kondisi aset saat menerima</li>
        </ul>
    </div>
</div>

{{-- ALUR PENGEMBALIAN --}}
<div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-6">
    <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-undo text-blue-500"></i> Alur Pengembalian Aset
    </h3>
    <div class="guide-step" style="border-left-color: #f59e0b;">
        <h4><span class="step-num" style="background:#f59e0b">1</span> Buka Menu Pengembalian</h4>
        <p>Klik <strong>Transaksi Pengembalian</strong> di sidebar.</p>
    </div>
    <div class="guide-step" style="border-left-color: #f59e0b;">
        <h4><span class="step-num" style="background:#f59e0b">2</span> Isi Form Pengembalian</h4>
        <ul>
            <li>Pilih aset yang akan dikembalikan</li>
            <li>Masukkan jumlah aset</li>
            <li>Pilih kondisi aset (Baik / Rusak Ringan / Rusak Berat / Hilang)</li>
            <li><strong class="text-red-600">WAJIB</strong> mengambil foto bukti kondisi aset saat dikembalikan</li>
            <li>Tambahkan catatan jika perlu</li>
        </ul>
    </div>
    <div class="guide-step" style="border-left-color: #f59e0b;">
        <h4><span class="step-num" style="background:#f59e0b">3</span> Kirim ke Admin</h4>
        <p>Klik <strong class="text-green-600">"Kirim ke Admin"</strong>. Admin akan memverifikasi pengembalian.</p>
    </div>
    <div class="guide-step" style="border-left-color: #f59e0b;">
        <h4><span class="step-num" style="background:#f59e0b">4</span> Selesai</h4>
        <p>Jika status berubah menjadi <strong class="text-green-600">Diterima</strong>, proses pengembalian selesai.</p>
        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-triangle"></i> Catatan: Jika aset rusak/hilang, akan ada prosedur lebih lanjut dari admin.</p>
    </div>
</div>

{{-- SYARAT & KETENTUAN --}}
<div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-6">
    <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-clipboard-list text-blue-500"></i> Syarat &amp; Ketentuan
    </h3>
    <div class="guide-step" style="border-left-color: #10b981;">
        <h4>Ketentuan Peminjaman</h4>
        <ul>
            <li>Peminjam wajib mengisi semua field dengan lengkap dan jujur</li>
            <li>Aset harus dikembalikan sesuai tanggal yang dijanjikan</li>
            <li>Keterlambatan pengembalian akan dicatat dalam sistem</li>
            <li>Maksimal durasi peminjaman: <strong>7 hari</strong> (kecuali ada izin khusus)</li>
            <li>Peminjam bertanggung jawab penuh atas aset yang dipinjam</li>
        </ul>
    </div>
    <div class="guide-step" style="border-left-color: #f59e0b;">
        <h4>Ketentuan Pengembalian</h4>
        <ul>
            <li>Wajib upload foto kondisi aset saat dikembalikan</li>
            <li>Jika aset rusak, peminjam wajib melapor ke admin</li>
            <li>Kerusakan aset menjadi tanggung jawab peminjam</li>
            <li>Pengembalian dilakukan di lokasi yang telah ditentukan sekolah</li>
        </ul>
    </div>
</div>

{{-- FAQ --}}
<div class="bg-white rounded-xl p-4 sm:p-5 md:p-6 shadow-sm border border-gray-200 mb-6">
    <h3 class="text-base md:text-lg font-semibold text-[#1e293b] mb-4 flex items-center gap-2">
        <i class="fas fa-question-circle text-blue-500"></i> FAQ (Pertanyaan Umum)
    </h3>
    @php
    $faqs = [
        ['q'=>'Berapa lama pengajuan diproses?', 'a'=>'Pengajuan peminjaman biasanya diproses dalam <strong>1–2 hari kerja</strong>. Kamu dapat memantau statusnya di menu Riwayat.'],
        ['q'=>'Apa yang terjadi jika terlambat mengembalikan?', 'a'=>'Keterlambatan akan tercatat di sistem dan dapat mempengaruhi pengajuan berikutnya. Segera hubungi admin jika ada kendala pengembalian.'],
        ['q'=>'Bagaimana jika aset rusak saat dipinjam?', 'a'=>'Laporkan kondisi kerusakan di form pengembalian. Pilih kondisi yang sesuai dan upload foto bukti. Admin akan menindaklanjuti sesuai prosedur.'],
        ['q'=>'Bisakah meminjam lebih dari satu aset sekaligus?', 'a'=>'Bisa! Gunakan fitur <strong>+Keranjang</strong> di halaman Data Aset untuk menambah beberapa aset, lalu proses sekaligus di Transaksi Peminjaman.'],
        ['q'=>'Bagaimana cara membatalkan pengajuan?', 'a'=>'Pengajuan yang masih berstatus Pending bisa dibatalkan dengan menghubungi admin. Pengajuan yang sudah Disetujui tidak bisa dibatalkan otomatis.'],
        ['q'=>'Apakah ada denda keterlambatan?', 'a'=>'Ya, keterlambatan pengembalian akan dikenakan denda sesuai kebijakan sekolah. Detail denda dapat dilihat di menu Riwayat setelah proses pengembalian.'],
    ];
    @endphp
    @foreach($faqs as $i => $faq)
    <div class="faq-item">
        <div class="faq-q" onclick="toggleFAQ(this)">
            <span>{{ $faq['q'] }}</span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="faq-a">
            <p>{!! $faq['a'] !!}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Kontak --}}
<div class="bg-gradient-to-br from-[#3b82f6] to-[#2563eb] rounded-xl p-5 md:p-6 text-white text-center mb-6">
    <h3 class="text-base md:text-lg font-bold mb-2">Butuh Bantuan?</h3>
    <p class="text-sm opacity-90 mb-4">Hubungi admin jika ada pertanyaan atau masalah</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="mailto:admin@nexora.sch.id" class="inline-flex items-center gap-2 bg-white text-[#3b82f6] px-5 py-2.5 rounded-full font-semibold text-sm hover:-translate-y-0.5 hover:shadow-lg transition-all">
            <i class="fas fa-envelope"></i> Email Admin
        </a>
        <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 bg-white/15 border border-white/30 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-white/25 transition-all">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function toggleFAQ(btn) {
        const answer = btn.nextElementSibling;
        const isOpen = answer.classList.contains('open');
        // Tutup semua
        document.querySelectorAll('.faq-a').forEach(a => a.classList.remove('open'));
        document.querySelectorAll('.faq-q').forEach(q => q.classList.remove('open'));
        // Buka yang diklik (jika belum terbuka)
        if (!isOpen) {
            answer.classList.add('open');
            btn.classList.add('open');
        }
    }
</script>
@endpush
