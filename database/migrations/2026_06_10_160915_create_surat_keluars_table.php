<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('kepada');
            $table->string('no_surat');
            $table->date('tanggal_surat');
            $table->string('dari');
            $table->date('tanggal_input');
            $table->text('perihal');
            $table->string('file_pdf')->nullable();
            $table->timestamps(); // Generates created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};