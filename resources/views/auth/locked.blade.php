<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Terkunci - InterOps Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #050b14;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 64, 175, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.15) 0px, transparent 50%);
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
        }
        .glass-panel {
            background: rgba(11, 22, 40, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-radius: 1.5rem;
        }
        .ops-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            transition: all 0.2s;
        }
        .ops-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
            outline: none;
        }
        .btn-unlock {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            color: white;
            transition: all 0.3s;
        }
        .btn-unlock:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }
    </style>
</head>
<body>

    <div class="glass-panel p-8 w-full max-w-md text-center transform transition-all">
        <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-ops-border shadow-lg">
            <i class="fas fa-lock text-3xl text-gray-400"></i>
        </div>
        
        <h2 class="text-2xl font-bold text-white mb-1">Sesi Terkunci</h2>
        <p class="text-sm text-gray-400 mb-6">Sistem mengunci layar Anda otomatis karena tidak ada aktivitas selama 15 menit.</p>
        
        <div class="bg-black/30 rounded-xl p-4 mb-6 flex items-center gap-4 text-left border border-white/5">
            <div class="w-12 h-12 rounded-full bg-blue-900/50 flex items-center justify-center border border-blue-500/30">
                <i class="fas fa-user-shield text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-400">Login sebagai</p>
                <p class="font-bold text-white">{{ auth()->user()->nama }}</p>
            </div>
        </div>

        <form id="unlockForm" class="space-y-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                    <i class="fas fa-key text-xs"></i>
                </span>
                <input type="password" id="password" required
                    class="ops-input w-full pl-10 pr-4 py-3 rounded-lg text-sm text-center tracking-widest"
                    placeholder="Masukkan Password">
            </div>
            
            <button type="submit" id="btnUnlock" class="btn-unlock w-full py-3 rounded-lg font-bold text-sm tracking-wide">
                <i class="fas fa-unlock-alt mr-2"></i> BUKA KUNCI
            </button>
        </form>

        <div class="mt-6 text-xs text-gray-500">
            Bukan {{ auth()->user()->nama }}? <a href="{{ url('/logout') }}" class="text-blue-400 hover:text-blue-300">Logout & Ganti Akun</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // Auto focus on load
            $('#password').focus();

            $('#unlockForm').on('submit', function(e) {
                e.preventDefault();
                let pwd = $('#password').val();
                
                $('#btnUnlock').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> MEMVALIDASI...');

                $.ajax({
                    url: "{{ url('/api/auth/unlock') }}",
                    type: "POST",
                    data: { password: pwd },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terbuka',
                            text: 'Selamat datang kembali!',
                            timer: 1000,
                            showConfirmButton: false,
                            background: '#0b1628',
                            color: '#e2e8f0'
                        }).then(() => {
                            // Go back to the previous page the user was on
                            window.history.back();
                            // Fallback if history is empty
                            setTimeout(() => { window.location.href = "{{ url('/dashboard') }}"; }, 500);
                        });
                    },
                    error: function(xhr) {
                        $('#btnUnlock').prop('disabled', false).html('<i class="fas fa-unlock-alt mr-2"></i> BUKA KUNCI');
                        $('#password').val('').focus();
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Password salah!',
                            background: '#0b1628',
                            color: '#e2e8f0'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
