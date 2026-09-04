<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add subbag column to users and change role enum
        Schema::table('users', function (Blueprint $table) {
            $table->string('subbag', 20)->nullable()->after('role');
        });

        // Drop the old enum constraint (MySQL) and recreate with new values
        // We use string instead of enum for flexibility
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'anggota'");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('subbag');
        });
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','pimpinan','operator','staf') NOT NULL");
    }
};
