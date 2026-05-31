<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryOutJawaban extends Model
{
    protected $table = 'try_out_jawabans';
    protected $fillable = [
        'try_out_id', 'user_id', 'soal_id', 'jawaban', 'is_benar', 'submitted_at'
    ];

    public function user()
    {
        return $this->belongsTo(Siswa::class, 'user_id');
    }
}
