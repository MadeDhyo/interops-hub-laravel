@extends('layout.main')

@section('title', 'Kelola Pengguna - InterOps-Hub')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide">Kelola Pengguna</h1>
            <p class="text-sm text-gray-400 mt-1">Manajemen personel, hak akses peran, dan penugasan subbag</p>
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
                    <tr class="border-b border-ops-border text-[10px] font-mono font-semibold uppercase tracking-widest text-slate-500 bg-ops-abyss/40">
                        <th class="py-4 px-6 w-16">No</th>
                        <th class="py-4 px-6">Nama Lengkap</th>
                        <th class="py-4 px-6">Username</th>
                        <th class="py-4 px-6">Role</th>
                        <th class="py-4 px-6">Subbag</th>
                        <th class="py-4 px-6 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userTableBody" class="text-sm divide-y divide-ops-border">
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400 font-mono text-xs">
                            <i class="fas fa-spinner fa-spin mr-2 text-ops-cyan"></i> Memuat data pengguna...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="userModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
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
                <input type="text" name="nama_lengkap" required class="w-full ops-input rounded-lg px-4 py-2.5 text-sm text-gray-100 focus:outline-none" placeholder="Masukkan nama lengkap...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Username</label>
                <input type="text" name="username" required class="w-full ops-input rounded-lg px-4 py-2.5 text-sm text-gray-100 focus:outline-none" placeholder="Masukkan username unik...">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Role Akses</label>
                    <select name="role" id="add_role" required onchange="toggleSubbagSelect('add')" class="w-full ops-input rounded-lg px-3 py-2.5 text-sm text-gray-100 focus:outline-none cursor-pointer">
                        <option value="anggota">Anggota</option>
                        <option value="kasubbag">Kasubbag</option>
                        <option value="kabag">Kabag</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="add_subbag_wrap">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Subbag</label>
                    <select name="subbag" id="add_subbag" class="w-full ops-input rounded-lg px-3 py-2.5 text-sm text-gray-100 focus:outline-none cursor-pointer">
                        <option value="urmin">URMIN</option>
                        <option value="ops">OPS</option>
                        <option value="koor">KOOR</option>
                        <option value="bhi">BHI</option>
                        <option value="bi">BI</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password Awal</label>
                <input type="password" name="password" required class="w-full ops-input rounded-lg px-4 py-2.5 text-sm text-gray-100 focus:outline-none" placeholder="Minimal 6 karakter...">
            </div>
            <div class="pt-2 flex justify-end space-x-3">
                <button type="button" onclick="closeAddModal()" class="px-5 py-2.5 rounded-lg text-sm text-slate-400 hover:text-white border border-ops-border transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 btn-primary rounded-lg text-sm flex items-center space-x-2">
                    <i class="fas fa-save text-xs"></i>
                    <span>Simpan Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="editUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
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
                <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required class="w-full ops-input rounded-lg px-4 py-2.5 text-sm text-gray-100 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Username</label>
                <input type="text" id="edit_username" name="username" required class="w-full ops-input rounded-lg px-4 py-2.5 text-sm text-gray-100 focus:outline-none" placeholder="Masukkan username unik...">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Role Akses</label>
                    <select name="role" id="edit_role" required onchange="toggleSubbagSelect('edit')" class="w-full ops-input rounded-lg px-3 py-2.5 text-sm text-gray-100 focus:outline-none cursor-pointer">
                        <option value="anggota">Anggota</option>
                        <option value="kasubbag">Kasubbag</option>
                        <option value="kabag">Kabag</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="edit_subbag_wrap">
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Subbag</label>
                    <select name="subbag" id="edit_subbag" class="w-full ops-input rounded-lg px-3 py-2.5 text-sm text-gray-100 focus:outline-none cursor-pointer">
                        <option value="urmin">URMIN</option>
                        <option value="ops">OPS</option>
                        <option value="koor">KOOR</option>
                        <option value="bhi">BHI</option>
                        <option value="bi">BI</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password Baru</label>
                <input type="password" id="edit_password" name="password" class="w-full ops-input rounded-lg px-4 py-2.5 text-sm text-gray-100 focus:outline-none" placeholder="Kosongkan jika tidak ingin ganti...">
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

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
                        text: err?.message || 'Terjadi kesalahan sistem.',
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        });

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
                        text: err?.message || 'Terjadi kesalahan sistem.',
                        background: '#0b1628',
                        color: '#fff',
                        confirmButtonColor: '#ef4444'
                    });
                }
            });
        });
    });

    function toggleSubbagSelect(prefix) {
        let role = $(`#${prefix}_role`).val();
        if (role === 'admin' || role === 'kabag') {
            $(`#${prefix}_subbag_wrap`).addClass('opacity-30 pointer-events-none');
            $(`#${prefix}_subbag`).prop('disabled', true);
        } else {
            $(`#${prefix}_subbag_wrap`).removeClass('opacity-30 pointer-events-none');
            $(`#${prefix}_subbag`).prop('disabled', false);
        }
    }

    function fetchUsers() {
        $.ajax({
            url: "{{ url('/api/users') }}",
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status === 200) {
                    renderUserTable(res.data);
                }
            }
        });
    }

    function renderUserTable(data) {
        let html = '';
        let currentUsername = "{{ auth()->user()->username }}";

        if (!data || data.length === 0) {
            html = '<tr><td colspan="6" class="text-center py-8 text-gray-500 font-mono text-xs">Belum ada akun pengguna terdaftar.</td></tr>';
            $('#userTableBody').html(html);
            return;
        }

        data.forEach((user, index) => {
            let roleBadge = 'bg-gray-700 text-gray-300';
            if (user.role === 'admin') roleBadge = 'stamp stamp-red';
            else if (user.role === 'kabag') roleBadge = 'stamp stamp-pending';
            else if (user.role === 'kasubbag') roleBadge = 'stamp stamp-disposisi';
            else if (user.role === 'anggota') roleBadge = 'bg-ops-cyan/15 text-ops-cyan border border-ops-cyan/30';

            let safeName = escapeHtml(user.nama_lengkap);
            let safeUsername = escapeHtml(user.username);
            let safeRole = escapeHtml(user.role);
            let subbagBadge = user.subbag
                ? `<span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase bg-ops-gold/15 text-ops-gold border border-ops-gold/30">${escapeHtml(user.subbag)}</span>`
                : `<span class="text-slate-500 italic text-xs">-</span>`;

            let aksiTombol = '';
            if (user.username === currentUsername) {
                aksiTombol = `<span class="text-xs text-slate-500 italic font-medium">Akun Anda (Aktif)</span>`;
            } else {
                aksiTombol = `
                    <div class="flex items-center justify-center space-x-2">
                        <button onclick="openEditModal(${user.id}, '${safeName.replace(/'/g, "\\'")}', '${safeUsername.replace(/'/g, "\\'")}', '${safeRole}', '${user.subbag || ''}')" class="p-2 text-slate-400 hover:text-ops-cyan transition-colors" title="Edit Akun">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteUser(${user.id}, '${safeName.replace(/'/g, "\\'")}')" class="p-2 text-slate-400 hover:text-rose-400 transition-colors" title="Hapus Akun">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
            }

            html += `
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-4 px-6 text-gray-500 font-mono text-xs">${index + 1}</td>
                    <td class="py-4 px-6 font-semibold text-white">${safeName}</td>
                    <td class="py-4 px-6 font-mono text-xs text-gray-300">${safeUsername}</td>
                    <td class="py-4 px-6">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold capitalize ${roleBadge}">
                            ${safeRole}
                        </span>
                    </td>
                    <td class="py-4 px-6">${subbagBadge}</td>
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
                            text: xhr.responseJSON?.message || 'Gagal menghapus user.',
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
        toggleSubbagSelect('add');
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

    function openEditModal(id, nama, username, role, subbag) {
        $('#edit_user_id').val(id);
        $('#edit_nama_lengkap').val(nama);
        $('#edit_username').val(username);
        $('#edit_role').val(role || 'anggota');
        $('#edit_subbag').val(subbag || 'urmin');
        $('#edit_password').val('');
        toggleSubbagSelect('edit');

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