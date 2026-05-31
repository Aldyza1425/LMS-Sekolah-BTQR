<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulProgress extends Model
{
    protected $table = 'modul_progresses';

    protected $fillable = [
        'user_id',
        'modul_id',
        'materi_id',
        'persen',
        'selesai',
        'skor'
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class, 'modul_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function user()
    {
        return $this->belongsTo(Siswa::class, 'user_id');
    }
}
