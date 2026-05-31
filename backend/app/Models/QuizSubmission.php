<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'materi_id',
        'score',
        'correct_count',
        'total_count',
        'passed',
        'time_used',
        'answers',
        'tipe_kuis'
    ];

    protected $casts = [
        'answers' => 'array',
        'passed' => 'boolean'
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function user()
    {
        return $this->belongsTo(Siswa::class, 'user_id');
    }
}
