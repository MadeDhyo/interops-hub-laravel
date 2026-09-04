<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';

    protected $fillable = [
        'kepada', 'dari', 'perihal', 'tanggal_masuk', 'no_surat',
        'no_dispo', 'disposisi_kabag', 'disposisi_kasubag', 'file_pdf', 'status',
        'full_text_content',
    ];

    /**
     * Relasi ke pivot subbag tujuan.
     * Satu surat masuk bisa punya 1-2 subbag tujuan.
     */
    public function subbags()
    {
        return $this->hasMany(SuratMasukSubbag::class, 'surat_masuk_id');
    }

    /**
     * Helper: ambil list nama subbag tujuan sebagai array.
     */
    public function getSubbagListAttribute(): array
    {
        return $this->subbags->pluck('subbag')->toArray();
    }
}