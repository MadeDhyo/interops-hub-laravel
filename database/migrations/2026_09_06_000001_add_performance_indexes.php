<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    public function up(): void
    {
        // surat_masuk: pastikan kolom full_text_content ada
        if (!Schema::hasColumn('surat_masuk', 'full_text_content')) {
            Schema::table('surat_masuk', function (Blueprint $table) {
                $table->longText('full_text_content')->nullable()->after('status');
            });
        }

        // surat_masuk: index pada kolom filter & sort utama
        Schema::table('surat_masuk', function (Blueprint $table) {
            if (!$this->indexExists('surat_masuk', 'idx_surma_status')) {
                $table->index('status', 'idx_surma_status');
            }
            if (!$this->indexExists('surat_masuk', 'idx_surma_tanggal')) {
                $table->index('tanggal_masuk', 'idx_surma_tanggal');
            }
        });

        // surat_masuk_subbag: index
        Schema::table('surat_masuk_subbag', function (Blueprint $table) {
            if (!$this->indexExists('surat_masuk_subbag', 'idx_sms_subbag')) {
                $table->index('subbag', 'idx_sms_subbag');
            }
        });

        // surat_keluar: pastikan kolom full_text_content ada
        if (!Schema::hasColumn('surat_keluar', 'full_text_content')) {
            Schema::table('surat_keluar', function (Blueprint $table) {
                $table->longText('full_text_content')->nullable()->after('perihal');
            });
        }

        // surat_keluar: index
        Schema::table('surat_keluar', function (Blueprint $table) {
            if (!$this->indexExists('surat_keluar', 'idx_surkel_subbag')) {
                $table->index('subbag', 'idx_surkel_subbag');
            }
            if (!$this->indexExists('surat_keluar', 'idx_surkel_status_paraf')) {
                $table->index('status_paraf_kabag', 'idx_surkel_status_paraf');
            }
            if (!$this->indexExists('surat_keluar', 'idx_surkel_tanggal')) {
                $table->index('tanggal_surat', 'idx_surkel_tanggal');
            }
        });

        // activity_logs: index
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!$this->indexExists('activity_logs', 'idx_actlogs_subbag')) {
                $table->index('subbag', 'idx_actlogs_subbag');
            }
            if (!$this->indexExists('activity_logs', 'idx_actlogs_created_at')) {
                $table->index('created_at', 'idx_actlogs_created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuk', function (Blueprint $table) {
            if ($this->indexExists('surat_masuk', 'idx_surma_status'))   $table->dropIndex('idx_surma_status');
            if ($this->indexExists('surat_masuk', 'idx_surma_tanggal'))  $table->dropIndex('idx_surma_tanggal');
            if (Schema::hasColumn('surat_masuk', 'full_text_content'))   $table->dropColumn('full_text_content');
        });

        Schema::table('surat_masuk_subbag', function (Blueprint $table) {
            if ($this->indexExists('surat_masuk_subbag', 'idx_sms_subbag')) $table->dropIndex('idx_sms_subbag');
        });

        Schema::table('surat_keluar', function (Blueprint $table) {
            if ($this->indexExists('surat_keluar', 'idx_surkel_subbag'))        $table->dropIndex('idx_surkel_subbag');
            if ($this->indexExists('surat_keluar', 'idx_surkel_status_paraf'))  $table->dropIndex('idx_surkel_status_paraf');
            if ($this->indexExists('surat_keluar', 'idx_surkel_tanggal'))       $table->dropIndex('idx_surkel_tanggal');
            if (Schema::hasColumn('surat_keluar', 'full_text_content'))         $table->dropColumn('full_text_content');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            if ($this->indexExists('activity_logs', 'idx_actlogs_subbag'))      $table->dropIndex('idx_actlogs_subbag');
            if ($this->indexExists('activity_logs', 'idx_actlogs_created_at'))  $table->dropIndex('idx_actlogs_created_at');
        });
    }
};
