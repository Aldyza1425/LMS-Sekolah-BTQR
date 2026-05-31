<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriProgress extends Model
{
    protected $table = 'materi_progresses';

    protected $fillable = [
        'user_id',
        'materi_id',
        'is_pre_test_done',
        'pre_test_score',
        'is_content_read',
        'is_resume_submitted',
        'resume_text',
        'resume_score',
        'is_tugas_submitted',
        'tugas_file_path',
        'tugas_score',
        'is_post_test_done',
        'post_test_score',
        'is_completed'
    ];

    public function user()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }
}
