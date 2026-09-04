<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->string('subbag', 20)->nullable()->after('file_pdf');
            $table->text('keterangan_tujuan')->nullable()->after('subbag');
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['subbag', 'keterangan_tujuan']);
        });
    }
};
