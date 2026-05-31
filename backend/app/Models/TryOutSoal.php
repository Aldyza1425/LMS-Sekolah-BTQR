<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryOutSoal extends Model
{
    protected $table = 'try_out_soals';

    protected $fillable = [
        'try_out_id',
        'pertanyaan',
        'gambar',
        'tipe',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'jawaban',
        'bobot',
        'urutan'
    ];

    public function tryOut()
    {
        return $this->belongsTo(TryOut::class, 'try_out_id');
    }
}
