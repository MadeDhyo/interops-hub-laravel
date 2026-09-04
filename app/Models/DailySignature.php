<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySignature extends Model
{
    protected $table = 'daily_signatures';

    protected $fillable = [
        'user_id',
        'signature_path',
        'signature_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
