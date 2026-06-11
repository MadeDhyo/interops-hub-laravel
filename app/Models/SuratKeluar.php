<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    // Explicitly target the singular table name to match your migration
    protected $table = 'surat_keluar';

    protected $fillable = [
        'kepada',
        'no_surat',
        'tanggal_surat',
        'dari',
        'tanggal_input',
        'perihal',
        'file_pdf'
    ];
}