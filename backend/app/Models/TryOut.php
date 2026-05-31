<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryOut extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'mulai_at',
        'selesai_at',
        'durasi_menit',
        'nilai_lulus',
        'is_active',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(Pengajar::class, 'created_by');
    }

    public function soals()
    {
        return $this->hasMany(TryOutSoal::class, 'try_out_id')->orderBy('urutan', 'asc');
    }
}
