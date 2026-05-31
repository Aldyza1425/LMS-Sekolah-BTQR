<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'siswas';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'phone',
        'domisili',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function modul_progresses()
    {
        return $this->hasMany(ModulProgress::class, 'user_id');
    }

    public function materi_progresses()
    {
        return $this->hasMany(MateriProgress::class, 'user_id');
    }

    public function quiz_submissions()
    {
        return $this->hasMany(QuizSubmission::class, 'user_id');
    }

    public function try_out_hasil()
    {
        return $this->hasMany(TryOutHasil::class, 'user_id');
    }

    public function try_out_jawabans()
    {
        return $this->hasMany(TryOutJawaban::class, 'user_id');
    }
}
