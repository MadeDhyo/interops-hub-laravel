<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    // Mengambil semua data user
    public function index()
    {
        Gate::authorize('akses-admin');
        
        $users = User::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200,
            'data' => $users
        ]);
    }

    // Menyimpan user baru ke database
    public function store(Request $request)
    {
        Gate::authorize('akses-admin');

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:users,username|max:50',
            'role'         => 'required|in:admin,kabag,kasubbag,anggota,pimpinan,staf',
            'subbag'       => 'nullable|in:bhi,bi,ops,koor,urmin',
            'password'     => 'required|string|min:6'
        ]);

        $subbag = in_array($request->role, ['admin', 'kabag']) ? null : $request->subbag;

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'role'         => $request->role,
            'subbag'       => $subbag,
            'password'     => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $user
        ]);
    }

    // Memperbarui user berdasarkan ID
    public function update(Request $request, $id)
    {
        Gate::authorize('akses-admin');

        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'sometimes|required|string|max:255',
            'username'     => 'required|string|max:50|unique:users,username,'.$id,
            'role'         => 'sometimes|required|in:admin,kabag,kasubbag,anggota,pimpinan,staf',
            'subbag'       => 'nullable|in:bhi,bi,ops,koor,urmin',
            'password'     => 'nullable|string|min:6'
        ]);

        $updateData = [
            'username' => $request->username,
        ];

        if ($request->filled('nama_lengkap')) {
            $updateData['nama_lengkap'] = $request->nama_lengkap;
        }

        if ($request->filled('role')) {
            $updateData['role'] = $request->role;
            $updateData['subbag'] = in_array($request->role, ['admin', 'kabag']) ? null : $request->subbag;
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'status' => 200,
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $user
        ]);
    }

    // Menghapus user berdasarkan ID
    public function destroy($id)
    {
        Gate::authorize('akses-admin');

        $user = User::findOrFail($id);
        
        // Proteksi mutlak: Cek id user yang sedang login via Session Web biasa
        if (\Illuminate\Support\Facades\Auth::id() == $user->id) {
            return response()->json([
                'status' => 400,
                'message' => 'Aksi Ditolak! Anda tidak diizinkan menghapus akun Anda sendiri yang sedang aktif digunakan.'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}