<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tindak_lanjut_surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_user');
            $table->string('tipe_aksi', 30); // 'tindak_lanjut', 'arsip', 'buat_balasan', 'lainnya'
            $table->string('no_balasan')->nullable();
            $table->text('catatan');
            $table->timestamps();

            $table->unique('surat_masuk_id'); // 1 tindak lanjut per surat
            $table->index('user_id', 'idx_tl_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tindak_lanjut_surat_masuk');
    }
};
