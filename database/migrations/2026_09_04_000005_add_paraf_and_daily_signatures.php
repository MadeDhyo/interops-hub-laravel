<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add paraf columns to surat_keluar
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->string('status_paraf_kabag', 20)->default('pending')->after('keterangan_tujuan');
            $table->timestamp('paraf_kabag_at')->nullable()->after('status_paraf_kabag');
            $table->text('catatan_kabag')->nullable()->after('paraf_kabag_at');
            $table->string('paraf_path', 255)->nullable()->after('catatan_kabag');
        });

        // 2. Create daily_signatures table for signature reuse
        Schema::create('daily_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('signature_path', 255);
            $table->date('signature_date');
            $table->timestamps();

            $table->unique(['user_id', 'signature_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_signatures');

        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['status_paraf_kabag', 'paraf_kabag_at', 'catatan_kabag', 'paraf_path']);
        });
    }
};
