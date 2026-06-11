<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('surat_masuk', function (Blueprint $table) {
        $table->id();
        $table->string('kepada');
        $table->string('dari');
        $table->text('perihal');
        $table->date('tanggal_masuk');
        $table->string('no_surat');
        $table->string('no_dispo')->nullable();
        $table->string('disposisi_kabag')->nullable();
        $table->string('disposisi_kasubag')->nullable();
        $table->string('file_pdf')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps(); // Generates created_at and updated_at automatically
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
