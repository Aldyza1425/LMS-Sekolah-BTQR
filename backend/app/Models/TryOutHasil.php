<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryOutHasil extends Model
{
    protected $table = 'try_out_hasil';
    protected $fillable = [
        'try_out_id', 'user_id', 'nilai', 'lulus', 'mulai_at', 'selesai_at', 'retake_status'
    ];

    public function tryOut()
    {
        return $this->belongsTo(TryOut::class, 'try_out_id');
    }

    public function user()
    {
        return $this->belongsTo(Siswa::class, 'user_id');
    }
}
