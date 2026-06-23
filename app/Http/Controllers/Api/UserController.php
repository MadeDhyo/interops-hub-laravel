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
            'username' => 'required|string|unique:users,username|max:50',
            'role' => 'required|in:admin,pimpinan,staf',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'role' => $request->role,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $user
        ]);
    }

    // Menghapus user berdasarkan ID
    public function destroy($id)
    {
        Gate::authorize('akses-admin');

        $user = User::findOrFail($id);
        
        // Mencegah admin menghapus dirinya sendiri pas login
        if (auth()->id() == $user->id) {
            return response()->json([
                'status' => 400,
                'message' => 'Anda tidak bisa menghapus akun Anda sendiri!'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}