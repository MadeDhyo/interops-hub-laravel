@extends('layout.main')

@section('title', 'Login - InterOps-Hub')

@section('content')
<div class="w-full max-w-md relative">
    <!-- Outer bracket decoration -->
    <div class="bracket-box glass-card rounded-xl shadow-2xl shadow-black/60 p-8 space-y-6">

        <!-- Classified header eyebrow -->
        <div class="text-center space-y-1">
            <p class="section-eyebrow" style="color: rgba(255,51,51,0.6); letter-spacing: 0.15em;">// AKSES TERBATAS //</p>
            <h1 class="text-2xl font-bold text-white mt-2 tracking-tight">Verifikasi Identitas</h1>
            <p class="text-xs text-slate-500 mt-1">Autentikasi diperlukan untuk mengakses sistem operasional</p>
        </div>

        <!-- Logo -->
        <div class="flex justify-center py-2">
            <div class="relative">
                <div class="absolute inset-0 rounded-full" style="background: radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%);"></div>
                <img src="{{ asset('images/logo-divhubinter.png') }}" alt="Logo Divhubinter" class="h-20 w-auto object-contain relative z-10">
            </div>
        </div>

        <!-- Divider -->
        <div class="flex items-center space-x-3">
            <div class="flex-1 border-t border-ops-border"></div>
            <span class="font-mono text-[10px] text-slate-600 tracking-widest">SYSLOGIN v2.1</span>
            <div class="flex-1 border-t border-ops-border"></div>
        </div>

        <form id="loginForm" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="username" class="section-eyebrow block">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-600">
                        <i class="fas fa-user text-xs"></i>
                    </span>
                    <input type="text" id="username" name="username" required
                        class="ops-input w-full pl-10 pr-4 py-3 rounded-lg text-sm"
                        placeholder="identifier...">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="section-eyebrow block">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-600">
                        <i class="fas fa-lock text-xs"></i>
                    </span>
                    <input type="password" id="password" name="password" required
                        class="ops-input w-full pl-10 pr-4 py-3 rounded-lg text-sm"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" id="btnSubmit"
                    class="btn-primary w-full py-3 rounded-lg text-sm flex justify-center items-center space-x-2">
                    <i class="fas fa-fingerprint text-xs"></i>
                    <span>AKSES SISTEM</span>
                </button>
            </div>
        </form>

        <!-- Footer stamp -->
        <div class="text-center pt-2">
            <p class="font-mono text-[9px] text-slate-700 tracking-widest uppercase">DIVHUBINTER POLRI · SISTEM TERKLASIFIKASI</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        let formData = {
            username: $('#username').val(),
            password: $('#password').val()
        };
        $('#btnSubmit').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');

        $.ajax({
            url: "{{ url('/login/attempt') }}",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Autentikasi Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#0b1628',
                        color: '#e2e8f0'
                    }).then(() => { window.location.href = response.redirect; });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON && xhr.responseJSON.message) errorMsg = xhr.responseJSON.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak',
                    text: errorMsg,
                    background: '#0b1628',
                    color: '#e2e8f0',
                    confirmButtonColor: '#00c6ff'
                });
                $('#btnSubmit').prop('disabled', false).html('<i class="fas fa-fingerprint text-xs"></i> <span>AKSES SISTEM</span>');
            }
        });
    });
});
</script>
@endpush