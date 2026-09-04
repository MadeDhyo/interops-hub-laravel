<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuk_subbag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->onDelete('cascade');
            $table->string('subbag', 20); // 'bhi','bi','ops','koor','urmin'
            $table->timestamps();

            $table->unique(['surat_masuk_id', 'subbag']); // No duplicate per surat
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuk_subbag');
    }
};
