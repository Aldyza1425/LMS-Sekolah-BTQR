<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Modul extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'arabic_title',
        'deskripsi',
        'mata_pelajaran',
        'level',
        'urutan',
        'thumbnail',
        'is_active',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($modul) {
            if (empty($modul->slug)) {
                $modul->slug = static::generateUniqueSlug($modul->judul);
            }
        });

        static::updating(function ($modul) {
            if ($modul->isDirty('judul') && empty($modul->slug)) {
                $modul->slug = static::generateUniqueSlug($modul->judul);
            }
        });
    }

    public static function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();
        return $count ? "{$slug}-" . ($count + 1) : $slug;
    }

    protected $appends = ['thumbnail_url'];

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail && !str_starts_with($this->thumbnail, 'http')) {
            return url('storage/' . $this->thumbnail);
        }
        return $this->thumbnail;
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function creator()
    {
        return $this->belongsTo(Pengajar::class, 'created_by');
    }
}
