@extends('layout.main')

@section('title', 'Kelola Pengguna - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Kelola Pengguna</h1>
            <p class="text-sm text-gray-400 mt-1">Manajemen hak akses data akun administrator, pimpinan, dan staf pelaksana</p>
        </div>
        <button onclick="openAddModal()" class="px-5 py-2.5 btn-primary rounded-lg transition-all duration-200 shadow-lg flex items-center space-x-2 text-sm">
            <i class="fas fa-user-plus text-xs"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Tabel Data User -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-ops-border text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-600 bg-ops-abyss/40">
                        <th class="py-4 px-6 w-20">No</th>
                        <th class="py-4 px-6">Nama Lengkap</th>
                        <th class="py-4 px-6">Username</th>
                        <th class="py-4 px-6">Role</th>
                        <th class="py-4 px-6 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="text-sm divide-y divide-ops-border">
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Memuat data pengguna...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="userModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="glass-card w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
        <div class="p-6 border-b border-ops-border flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-user-shield text-ops-cyan"></i>
                <span>Tambah Akun Baru</span>
            </h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addUserForm" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none  transition-colors" placeholder="Masukkan nama lengkap...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Username</label>
                <input type="text" name="username" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none  transition-colors" placeholder="Masukkan username unik...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Role Akses</label>
                <select name="role" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none  transition-colors">
                    <option value="staf">Staf (Pelaksana)</option>
                    <option value="pimpinan">Pimpinan (Validator)</option>
                    <option value="admin">Admin (Full Control)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password Awal</label>
                <input type="password" name="password" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none  transition-colors" placeholder="Minimal 6 karakter...">
            </div>
            <div class="pt-2 flex justify-end space-x-3">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white border border-ops-border px-4 py-2 transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 btn-primary rounded-lg text-sm flex items-center space-x-2">
                    <i class="fas fa-save text-xs"></i>
                    <span>Simpan Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50">
    <div class="glass-card w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalEditContent">
        <div class="p-6 border-b border-ops-border flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i class="fas fa-user-edit text-ops-cyan"></i>
                <span>Edit Pengguna</span>
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editUserForm" class="p-6 space-y-4">
            <input type="hidden" id="edit_user_id" name="id">
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input type="text" id="edit_nama_lengkap" readonly class="w-full bg-ops-abyss/40 border border-ops-border/40 rounded-lg px-4 py-3 text-sm text-gray-500 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Username</label>
                <input type="text" id="edit_username" name="username" required class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none transition-colors" placeholder="Masukkan username unik...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password Baru</label>
                <input type="password" id="edit_password" name="password" class="w-full ops-input rounded-lg px-4 py-3 text-sm text-gray-100 focus:outline-none transition-colors" placeholder="Kosongkan jika tidak ingin ganti...">
            </div>
            <div class="pt-2 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white border border-ops-border transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 btn-primary rounded-lg text-sm flex items-center space-x-2">
                    <i class="fas fa-save text-xs"></i>
                    <span>Update Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        fetchUsers();

        // Setup AJAX CSRF Token dari Meta Tag HTML
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Submit Form via AJAX
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: "{{ url('/api/users') }}",
                type: "POST",
                data: formData,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#00c6ff'
                    });
                    closeAddModal();
                    $('#addUserForm')[0].reset();
                    fetchUsers();
                },
                error: function(xhr) {
                    let err = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: err.message || 'Terjadi kesalahan sistem.',
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        });

        // Submit Form Edit via AJAX
        $('#editUserForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            let id = $('#edit_user_id').val();

            $.ajax({
                url: `{{ url('/api/users') }}/${id}`,
                type: "PUT",
                data: formData,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#00c6ff'
                    });
                    closeEditModal();
                    $('#editUserForm')[0].reset();
                    fetchUsers();
                },
                error: function(xhr) {
                    let err = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memperbarui',
                        text: err.message || 'Terjadi kesalahan sistem.',
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        });
    });

    function fetchUsers() {
        $.ajax({
            url: "{{ url('/api/users') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if(res.status === 200) {
                    renderUserTable(res.data);
                }
            }
        });
    }

    function renderUserTable(data) {
        let html = '';
        
        // Ambil nama username admin yang lagi login dari Laravel Blade (disuntik ke JS)
        let currentUsername = "{{ auth()->user()->username }}";

        data.forEach((user, index) => {
            let roleBadge = 'bg-gray-700 text-gray-300';
            if (user.role === 'admin') roleBadge = 'stamp stamp-red';
            else if (user.role === 'pimpinan') roleBadge = 'stamp stamp-pending';
            else if (user.role === 'staf') roleBadge = 'stamp stamp-disposisi';

            // Cek apakah baris ini adalah akun si admin yang sedang login
            let aksiTombol = '';
            if (user.username === currentUsername) {
                // Jika akun sendiri, kasih teks penanda (tombol hapus dihilangkan)
                aksiTombol = `<span class="text-xs text-gray-500 italic font-medium">Akun Anda (Aktif)</span>`;
            } else {
                // Jika akun orang lain, tombol hapus & edit muncul normal
                aksiTombol = `
                    <div class="flex items-center justify-center space-x-2">
                        <button onclick="openEditModal(${user.id}, '${user.nama_lengkap}', '${user.username}')" class="p-2 text-gray-500 hover:text-ops-cyan transition-colors" title="Edit Akun">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser(${user.id}, '${user.nama_lengkap}')" class="p-2 text-gray-500 hover:text-rose-400 transition-colors" title="Hapus Akun">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
            }

            html += `
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-4 px-6 text-gray-500 font-mono text-xs">${index + 1}</td>
                    <td class="py-4 px-6 font-semibold text-white">${user.nama_lengkap}</td>
                    <td class="py-4 px-6 font-mono text-xs text-gray-400">${user.username}</td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold capitalize ${roleBadge}">
                            ${user.role}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        ${aksiTombol}
                    </td>
                </tr>
            `;
        });
        $('#userTableBody').html(html);
    }

    function deleteUser(id, nama) {
        Swal.fire({
            title: 'Hapus Pengguna?',
            text: `Akun "${nama}" akan dihapus permanen dari sistem database.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#0b1628',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#0b1628',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('/api/users') }}/${id}`,
                    type: "DELETE",
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            background: '#0b1628',
                            color: '#fff',
                            confirmButtonColor: '#00c6ff'
                        });
                        fetchUsers();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Aksi Ditolak',
                            text: xhr.responseJSON.message || 'Gagal menghapus user.',
                            background: '#0b1628',
                            color: '#fff',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    }

    function openAddModal() {
        $('#userModal').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modalContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closeAddModal() {
        $('#modalContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#userModal').removeClass('flex').addClass('hidden');
        }, 300);
    }

    function openEditModal(id, nama, username) {
        $('#edit_user_id').val(id);
        $('#edit_nama_lengkap').val(nama);
        $('#edit_username').val(username);
        $('#edit_password').val('');

        $('#editUserModal').removeClass('hidden').addClass('flex');
        setTimeout(() => {
            $('#modalEditContent').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
        }, 10);
    }

    function closeEditModal() {
        $('#modalEditContent').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => {
            $('#editUserModal').removeClass('flex').addClass('hidden');
        }, 300);
    }
</script>
@endpush