<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasukSubbag extends Model
{
    protected $table = 'surat_masuk_subbag';

    protected $fillable = [
        'surat_masuk_id',
        'subbag',
    ];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }
}
