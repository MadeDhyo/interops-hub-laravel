@extends('layout.main')

@section('title', 'Login - InterOps-Hub')

@section('content')
<div class="w-full max-w-md bg-gray-800 rounded-2xl border border-gray-700 shadow-2xl p-8 space-y-6">
    <div class="text-center space-y-2">
        <h1 class="text-2xl font-bold text-white tracking-wide">Selamat Datang</h1>
        <p class="text-sm text-gray-400">Silakan login untuk mengakses InterOps-Hub</p>
    </div>

    <form id="loginForm" class="space-y-5">
        @csrf
        
        <div class="space-y-2">
            <label for="username" class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Username</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                    <i class="fas fa-user text-sm"></i>
                </span>
                <input type="text" id="username" name="username" required
                    class="w-full pl-11 pr-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all duration-200 text-sm"
                    placeholder="Masukkan username anda">
            </div>
        </div>

        <div class="space-y-2">
            <label for="password" class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-500">
                    <i class="fas fa-lock text-sm"></i>
                </span>
                <input type="password" id="password" name="password" required
                    class="w-full pl-11 pr-4 py-3 bg-gray-900 border border-gray-700 rounded-xl text-gray-100 placeholder-gray-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all duration-200 text-sm"
                    placeholder="••••••••">
            </div>
        </div>

        <button type="submit" id="btnSubmit"
            class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-indigo-600/30 flex justify-center items-center space-x-2 text-sm mt-2">
            <span>Masuk ke Sistem</span>
            <i class="fas fa-arrow-right text-xs"></i>
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Setup jQuery to always pass the CSRF token with AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
                        title: 'Login Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#1f2937',
                        color: '#fff'
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan sistem.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Login',
                    text: errorMsg,
                    background: '#1f2937',
                    color: '#fff',
                    confirmButtonColor: '#4f46e5'
                });
                
                $('#btnSubmit').prop('disabled', false).html('<span>Masuk ke Sistem</span> <i class="fas fa-arrow-right text-xs"></i>');
            }
        });
    });
});
</script>
@endpush