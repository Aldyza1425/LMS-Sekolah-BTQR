<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'modul_id',
        'judul',
        'deskripsi',
        'tipe',
        'file_path',
        'link_url',
        'durasi',
        'urutan',
        'has_pretest',
        'has_posttest',
    ];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function soals()
    {
        return $this->hasMany(MateriSoal::class);
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }

    public function progresses()
    {
        return $this->hasMany(MateriProgress::class);
    }
}
