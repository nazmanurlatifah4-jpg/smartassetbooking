@extends('layouts.admin')

@section('title', 'Master Data')
@section('page-subtitle', 'Master Data')

@push('styles')
<style>
    .tab { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; color: #64748b; background: transparent; border: none; }
    .tab.active { background: linear-gradient(to right, #3b82f6, #2563eb); color: white; box-shadow: 0 4px 12px rgba(59,130,246,0.3); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .field-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 5px; }
    .field-input { width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #1e293b; background: #f8fafc; outline: none; transition: border-color 0.2s; }
    .field-input:focus { border-color: #3b82f6; background: white; }
    select.field-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%2394a3b8' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 32px; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex items-center gap-3 mb-6">
    <div class="w-10 h-10 rounded-xl bg-[#dbeafe] flex items-center justify-center text-[#3b82f6] text-lg">
        <i class="fas fa-database"></i>
    </div>
    <div>
        <h2 class="text-xl font-bold text-[#1e293b]">Master Data</h2>
        <p class="text-xs text-[#64748b]">Kelola data pengguna dan aset sekolah</p>
    </div>
</div>

{{-- Tab Buttons --}}
<div class="flex gap-2 mb-4 bg-white p-1.5 rounded-xl shadow-sm border border-[#e2e8f0] w-fit">
    <button class="tab active" onclick="switchTab(event,'user')"><i class="fas fa-users mr-1.5"></i>Data User</button>
    <button class="tab" onclick="switchTab(event,'aset')"><i class="fas fa-box mr-1.5"></i>Data Aset</button>
</div>

{{-- ===== TAB USER ===== --}}
<div id="tab-user" class="tab-content active">
    <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0]">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="text-base font-semibold text-[#1e293b] flex items-center gap-2"><i class="fas fa-users text-[#3b82f6]"></i> Daftar User</h3>
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-xs"></i>
                    <input type="text" placeholder="Cari user..." oninput="filterTable('userTable',this.value)"
                        class="pl-8 pr-3 py-2 border border-[#e2e8f0] rounded-lg text-xs text-[#475569] focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc] w-full sm:w-48">
                </div>
                <button onclick="openModal('addUserModal')"
                    class="px-4 py-2 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
        </div>
        <div class="overflow-x-auto -mx-5 md:mx-0">
            <div class="inline-block min-w-full align-middle px-5 md:px-0">
                <table class="min-w-full text-sm border-collapse" id="userTable">
                    <thead class="bg-[#f8fafc]">
                        <tr>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Nama</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Email</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Role</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden lg:table-cell">Jurusan</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $users = [
                            ['no'=>1,'name'=>'Budi Santoso','email'=>'budi@email.com','role'=>'Peminjam','jurusan'=>'TKJ'],
                            ['no'=>2,'name'=>'Siti Nurhaliza','email'=>'siti@email.com','role'=>'Peminjam','jurusan'=>'Matematika'],
                            ['no'=>3,'name'=>'Ahmad Ridwan','email'=>'ahmad@email.com','role'=>'Peminjam','jurusan'=>'RPL'],
                            ['no'=>4,'name'=>'Dewi Lestari','email'=>'dewi@email.com','role'=>'Manajemen','jurusan'=>'-'],
                            ['no'=>5,'name'=>'Eko Prasetyo','email'=>'eko@email.com','role'=>'Peminjam','jurusan'=>'Multimedia'],
                        ];
                        @endphp
                        @foreach($users as $u)
                        <tr class="hover:bg-[#f8fafc] transition-colors">
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $u['no'] }}</td>
                            <td class="p-3 text-xs text-[#1e293b] border-b border-[#f1f5f9] font-medium">{{ $u['name'] }}</td>
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $u['email'] }}</td>
                            <td class="p-3 border-b border-[#f1f5f9] hidden sm:table-cell">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $u['role']==='Manajemen' ? 'bg-[#e9d5ff] text-[#7e22ce]' : 'bg-[#dbeafe] text-[#1d4ed8]' }}">{{ $u['role'] }}</span>
                            </td>
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden lg:table-cell">{{ $u['jurusan'] }}</td>
                            <td class="p-3 border-b border-[#f1f5f9]">
                                <div class="flex gap-1">
                                    <button onclick="openEditUserModal(this, '{{ $u['id'] ?? $u['no'] }}')"
    data-name="{{ $u['name'] }}" 
    data-email="{{ $u['email'] }}" 
    data-role="{{ $u['role'] }}" 
    data-jurusan="{{ $u['jurusan'] }}"
    class="w-7 h-7 rounded-md bg-[#dbeafe] text-[#3b82f6] hover:bg-[#3b82f6] hover:text-white transition-all flex items-center justify-center text-xs">
    <i class="fas fa-edit"></i>
</button>

<button onclick="openDeleteModal('user', '{{ $u['name'] }}', '{{ $u['id'] ?? $u['no'] }}')"
    class="w-7 h-7 rounded-md bg-[#fee2e2] text-[#ef4444] hover:bg-[#ef4444] hover:text-white transition-all flex items-center justify-center text-xs">
    <i class="fas fa-trash"></i>
</button>                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===== TAB ASET ===== --}}
<div id="tab-aset" class="tab-content">
    <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-[#e2e8f0]">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h3 class="text-base font-semibold text-[#1e293b] flex items-center gap-2"><i class="fas fa-box text-[#3b82f6]"></i> Daftar Aset</h3>
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#94a3b8] text-xs"></i>
                    <input type="text" placeholder="Cari aset..." oninput="filterTable('asetTable',this.value)"
                        class="pl-8 pr-3 py-2 border border-[#e2e8f0] rounded-lg text-xs text-[#475569] focus:outline-none focus:border-[#3b82f6] bg-[#f8fafc] w-full sm:w-48">
                </div>
                <button onclick="openModal('addAsetModal')"
                    class="px-4 py-2 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all">
                    <i class="fas fa-plus"></i> Tambah Aset
                </button>
            </div>
        </div>
        <div class="overflow-x-auto -mx-5 md:mx-0">
            <div class="inline-block min-w-full align-middle px-5 md:px-0">
                <table class="min-w-full text-sm border-collapse" id="asetTable">
                    <thead class="bg-[#f8fafc]">
                        <tr>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">No</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Nama Aset</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden sm:table-cell">Kategori</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Kondisi</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0] hidden md:table-cell">Stok</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Status</th>
                            <th class="p-3 text-left text-xs font-semibold text-[#64748b] border-b-2 border-[#e2e8f0]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @php
                        $asets = [
                            ['no'=>1,'nama'=>'Laptop Lenovo ThinkPad','kat'=>'Elektronik','kondisi'=>'Baik','stok'=>5,'status'=>'Tersedia'],
                            ['no'=>2,'nama'=>'Proyektor Epson EB-X41','kat'=>'Elektronik','kondisi'=>'Baik','stok'=>3,'status'=>'Tersedia'],
                            ['no'=>3,'nama'=>'Kamera Canon EOS 1500D','kat'=>'Fotografi','kondisi'=>'Baik','stok'=>2,'status'=>'Dipinjam'],
                            ['no'=>4,'nama'=>'Sound System Simbadda','kat'=>'Audio','kondisi'=>'Cukup','stok'=>1,'status'=>'Tersedia'],
                            ['no'=>5,'nama'=>'iPad Pro 12.9"','kat'=>'Elektronik','kondisi'=>'Baik','stok'=>4,'status'=>'Tersedia'],
                            ['no'=>6,'nama'=>'Drone DJI Mini 3','kat'=>'Fotografi','kondisi'=>'Baik','stok'=>1,'status'=>'Maintenance'],
                        ];
                        @endphp --}}
                        @foreach($asets as $a)
                        @php
                            $sc = match($a['status']) {
                                'Tersedia' => 'bg-[#d1fae5] text-[#065f46]',
                                'Dipinjam' => 'bg-[#fed7aa] text-[#c2410c]',
                                default => 'bg-[#e0e7ff] text-[#3730a3]'
                            };
                        @endphp
                        <tr class="hover:bg-[#f8fafc] transition-colors">
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9]">{{ $a['no'] }}</td>
                            <td class="p-3 text-xs text-[#1e293b] border-b border-[#f1f5f9] font-medium">{{ $a['nama_aset'] }}</td>
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden sm:table-cell">{{ $a['kat'] }}</td>
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $a['kondisi'] }}</td>
                            <td class="p-3 text-xs text-[#475569] border-b border-[#f1f5f9] hidden md:table-cell">{{ $a['stok'] }}</td>
                            <td class="p-3 border-b border-[#f1f5f9]">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">{{ $a['status'] }}</span>
                            </td>
                            <td class="p-3 border-b border-[#f1f5f9]">
                                <div class="flex gap-1">
                                    {{-- Ganti tombol di tabel Aset menjadi seperti ini --}}
<button onclick="openEditAsetModal(this, '{{ $a['id'] ?? $a['no'] }}')"
    data-nama_aset="{{ $a['nama_aset'] }}" 
    data-kat="{{ $a['kat'] }}" 
    data-kondisi="{{ $a['kondisi'] }}" 
    data-stok="{{ $a['stok'] }}"
    class="w-7 h-7 rounded-md bg-[#dbeafe] text-[#3b82f6] hover:bg-[#3b82f6] hover:text-white transition-all flex items-center justify-center text-xs">
    <i class="fas fa-edit"></i>
</button>

<button onclick="openDeleteModal('aset', '{{ $a['nama_aset'] }}', '{{ $a['id'] ?? $a['no'] }}')"
    class="w-7 h-7 rounded-md bg-[#fee2e2] text-[#ef4444] hover:bg-[#ef4444] hover:text-white transition-all flex items-center justify-center text-xs">
    <i class="fas fa-trash"></i>
</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Add User Modal --}}
<div id="addUserModal" class="modal-overlay" onclick="if(event.target===this)closeModal('addUserModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-user-plus mr-2"></i>Tambah User</h3>
            <button onclick="closeModal('addUserModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="field-label">Nama Lengkap</label>
                <input type="text" name="name" placeholder="Nama lengkap..." class="field-input" required>
            </div>
            <div>
                <label class="field-label">Email</label>
                <input type="email" name="email" placeholder="email@sekolah.id" class="field-input" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Role</label>
                    <select name="role" class="field-input">
                        <option value="peminjam">Peminjam</option>
                        <option value="manajemen">Manajemen</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Jurusan</label>
                    <input type="text" name="jurusan" placeholder="Jurusan/Dept." class="field-input">
                </div>
            </div>
            <div>
                <label class="field-label">Password</label>
                <input type="password" name="password" placeholder="Password..." class="field-input" required>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <button type="button" onclick="closeModal('addUserModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit User Modal --}}
<div id="editUserModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editUserModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#f59e0b] to-[#d97706] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-user-edit mr-2"></i>Edit User</h3>
            <button onclick="closeModal('editUserModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.users.update', ':id') }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="field-label">Nama Lengkap</label>
                <input type="text" id="editUserName" name="name" class="field-input" required>
            </div>
            <div>
                <label class="field-label">Email</label>
                <input type="email" id="editUserEmail" name="email" class="field-input" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Role</label>
                    <select id="editUserRole" name="role" class="field-input">
                        <option value="peminjam">Peminjam</option>
                        <option value="manajemen">Manajemen</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Jurusan</label>
                    <input type="text" id="editUserJurusan" name="jurusan" class="field-input">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#f59e0b] to-[#d97706] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
                <button type="button" onclick="closeModal('editUserModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Aset Modal --}}
<div id="addAsetModal" class="modal-overlay" onclick="if(event.target===this)closeModal('addAsetModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#3b82f6] to-[#2563eb] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-box-open mr-2"></i>Tambah Aset</h3>
            <button onclick="closeModal('addAsetModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.aset.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="field-label">Nama Aset</label>
                <input type="text" name="nama" placeholder="Nama aset..." class="field-input" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Kategori</label>
                    <select name="kategori" class="field-input">
                        <option>Elektronik</option>
                        <option>Fotografi</option>
                        <option>Audio</option>
                        <option>Olahraga</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Kondisi</label>
                    <select name="kondisi" class="field-input">
                        <option>Baik</option>
                        <option>Cukup</option>
                        <option>Rusak</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Jumlah Stok</label>
                    <input type="number" name="stok" min="1" value="1" class="field-input" required>
                </div>
                <div>
                    <label class="field-label">Denda/Hari (Rp)</label>
                    <input type="number" name="denda_per_hari" placeholder="5000" class="field-input">
                </div>
            </div>
            <div>
                <label class="field-label">Deskripsi</label>
                <textarea name="deskripsi" placeholder="Keterangan aset..." rows="2" class="field-input resize-none"></textarea>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#3b82f6] to-[#2563eb] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
                <button type="button" onclick="closeModal('addAsetModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Aset Modal --}}
<div id="editAsetModal" class="modal-overlay" onclick="if(event.target===this)closeModal('editAsetModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="bg-gradient-to-r from-[#f59e0b] to-[#d97706] px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-white font-semibold text-base"><i class="fas fa-edit mr-2"></i>Edit Aset</h3>
            <button onclick="closeModal('editAsetModal')" class="text-white/80 hover:text-white text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.aset.update', ':id') }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="field-label">Nama Aset</label>
                <input type="text" id="editAsetNama" name="nama" class="field-input" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Kategori</label>
                    <select id="editAsetKat" name="kategori" class="field-input">
                        <option>Elektronik</option><option>Fotografi</option><option>Audio</option><option>Olahraga</option><option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="field-label">Kondisi</label>
                    <select id="editAsetKondisi" name="kondisi" class="field-input">
                        <option>Baik</option><option>Cukup</option><option>Rusak</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="field-label">Stok</label>
                <input type="number" id="editAsetStok" name="stok" min="0" class="field-input">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 py-2.5 bg-gradient-to-r from-[#f59e0b] to-[#d97706] text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity">
                    <i class="fas fa-save mr-1"></i> Update
                </button>
                <button type="button" onclick="closeModal('editAsetModal')" class="px-4 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)closeModal('deleteModal')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="bg-[#fef2f2] px-6 py-5 text-center">
            <div class="w-14 h-14 rounded-full bg-[#fee2e2] flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-trash-alt text-[#ef4444] text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-[#1e293b] mb-1">Konfirmasi Hapus</h3>
            <p class="text-sm text-[#64748b]">Yakin ingin menghapus <strong id="deleteTarget"></strong>?<br>Data tidak dapat dikembalikan.</p>
        </div>
        <div class="px-6 py-4 flex gap-2">
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-2.5 bg-[#ef4444] text-white rounded-lg text-sm font-semibold hover:bg-[#dc2626] transition-colors">
                    Ya, Hapus
                </button>
            </form>
            <button onclick="closeModal('deleteModal')" class="flex-1 py-2.5 bg-[#f1f5f9] text-[#64748b] rounded-lg text-sm font-semibold hover:bg-[#e2e8f0] transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@push('scripts')
<script>
    // Fungsi Dasar Modal
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // Navigasi Tab
    function switchTab(e, tab) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        e.currentTarget.classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
    }

    // Logic Edit User
    function openEditUserModal(btn, id) {
        const modal = document.getElementById('editUserModal');
        const form = modal.querySelector('form');
        
        // Update URL action form
        let url = "{{ route('admin.users.update', ':id') }}";
        form.action = url.replace(':id', id);

        // Isi field
        document.getElementById('editUserName').value = btn.dataset.name;
        document.getElementById('editUserEmail').value = btn.dataset.email;
        document.getElementById('editUserRole').value = btn.dataset.role.toLowerCase();
        document.getElementById('editUserJurusan').value = btn.dataset.jurusan;
        
        openModal('editUserModal');
    }

    // Logic Edit Aset
    function openEditAsetModal(btn, id) {
        const modal = document.getElementById('editAsetModal');
        const form = modal.querySelector('form');
        
        // Update URL action form
        let url = "{{ route('admin.aset.update', ':id') }}";
        form.action = url.replace(':id', id);

        // Isi field
        document.getElementById('editAsetNama').value = btn.dataset.nama_aset;
        document.getElementById('editAsetKat').value = btn.dataset.kat;
        document.getElementById('editAsetKondisi').value = btn.dataset.kondisi;
        document.getElementById('editAsetStok').value = btn.dataset.stok;
        
        openModal('editAsetModal');
    }

    // Logic Delete (Global)
    function openDeleteModal(type, name, id) {
        document.getElementById('deleteTarget').textContent = name;
        const form = document.getElementById('deleteForm');
        
        // Tentukan route berdasarkan type (user atau aset)
        let url = (type === 'user') 
            ? "{{ route('admin.users.destroy', ':id') }}" 
            : "{{ route('admin.aset.destroy', ':id') }}";
            
        form.action = url.replace(':id', id);
        openModal('deleteModal');
    }

    // Filter Search
    function filterTable(tableId, query) {
        const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(query.toLowerCase()) ? '' : 'none';
        });
    }
</script>
@endpush
