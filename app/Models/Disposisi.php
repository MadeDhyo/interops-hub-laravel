<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    protected $table = 'disposisi';

    protected $fillable = ['surat_id', 'staf_id', 'catatan'];

    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_id');
    }

    public function staf()
    {
        return $this->belongsTo(User::class, 'staf_id');
    }
}