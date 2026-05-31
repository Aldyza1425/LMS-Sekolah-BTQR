<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriSoal extends Model
{
    protected $fillable = [
        'materi_id',
        'soal',
        'a',
        'b',
        'c',
        'd',
        'jawaban',
        'media_type',
        'video_link',
        'video_path',
        'pdf_path',
        'tipe_soal',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}
