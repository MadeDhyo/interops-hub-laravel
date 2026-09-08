<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakLanjutSuratMasuk extends Model
{
    protected $table = 'tindak_lanjut_surat_masuk';

    protected $fillable = [
        'surat_masuk_id', 'user_id', 'nama_user',
        'tipe_aksi', 'no_balasan', 'catatan',
    ];

    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i',
        'updated_at' => 'datetime:d/m/Y H:i',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
