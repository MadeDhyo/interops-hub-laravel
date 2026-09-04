<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    protected $table = 'surat_keluar';

    protected $fillable = [
        'kepada',
        'no_surat',
        'tanggal_surat',
        'dari',
        'tanggal_input',
        'perihal',
        'file_pdf',
        'full_text_content',
        'subbag',
        'keterangan_tujuan',
        'status_paraf_kabag',
        'paraf_kabag_at',
        'catatan_kabag',
        'paraf_path',
    ];
}