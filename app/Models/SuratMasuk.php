<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';
    
    protected $fillable = [
        'kepada', 'dari', 'perihal', 'tanggal_masuk', 'no_surat', 
        'no_dispo', 'disposisi_kabag', 'disposisi_kasubag', 'file_pdf', 'status'
    ];
}